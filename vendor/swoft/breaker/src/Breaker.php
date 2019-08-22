<?php declare(strict_types=1);


namespace Swoft\Breaker;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\Contract\StateInterface;
use Swoft\Breaker\Exception\BreakerException;
use Swoft\Breaker\State\CloseState;
use Swoft\Breaker\State\HalfOpenState;
use Swoft\Breaker\State\OpenState;
use Swoft\Log\Helper\Log;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoole\Coroutine\Channel;
use Throwable;

/**
 * Class Breaker
 *
 * @since 2.0
 *
 * @Bean(name="breaker", scope=Bean::PROTOTYPE)
 */
class Breaker
{
    use PrototypeTrait;

    /**
     * @var StateInterface
     */
    private $state;

    /**
     * @var int
     */
    private $failCount = 0;

    /**
     * @var int
     */
    private $sucCount = 0;

    /**
     * @var float
     */
    private $timeout = 0;

    /**
     * @var callable|array
     */
    private $fallback;

    /**
     * @var int
     */
    private $failThreshold = 3;

    /**
     * @var int
     */
    private $sucThreshold = 3;

    /**
     * @var bool
     */
    private $forceOpen = false;

    /**
     * @var bool
     */
    private $forceClose = false;

    /**
     * Seconds
     *
     * @var int
     */
    private $retryTime = 3;

    /**
     * @param array $config
     *
     * @return Breaker
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(array $config): self
    {
        $self = self::__instance();

        foreach ($config as $name => $value) {
            $self->{$name} = $value;
        }

        // Move to close by init
        $self->moveToClose();

        return $self;
    }

    /**
     * @return bool
     */
    public function isClose(): bool
    {
        return $this->state instanceof CloseState;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->state instanceof OpenState;
    }

    /**
     * @return bool
     */
    public function isHalfOpen(): bool
    {
        return $this->state instanceof HalfOpenState;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function moveToOpen(): void
    {
        $this->state = OpenState::new($this);
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function moveToClose(): void
    {
        $this->state = CloseState::new($this);
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function moveToHalfOpen(): void
    {
        $this->state = HalfOpenState::new($this);
    }

    /**
     * @return int
     */
    public function incSucCount(): int
    {
        $this->sucCount++;
        return $this->sucCount;
    }

    /**
     * Reset sucCount
     */
    public function resetSucCount(): void
    {
        $this->sucCount = 0;
    }

    /**
     * @return bool
     */
    public function isReachSucCount(): bool
    {
        return $this->sucCount >= $this->sucThreshold;
    }

    /**
     * @return int
     */
    public function incFailCount(): int
    {
        $this->failCount++;
        return $this->failCount;
    }

    /**
     * Reset failCount
     */
    public function resetFailCount(): void
    {
        $this->failCount = 0;
    }

    /**
     * @return bool
     */
    public function isReachFailThreshold(): bool
    {
        return $this->failCount >= $this->failThreshold;
    }

    /**
     * @return int
     */
    public function getSucThreshold(): int
    {
        return $this->sucThreshold;
    }

    /**
     * @return int
     */
    public function getRetryTime(): int
    {
        return $this->retryTime;
    }

    /**
     * @param object         $target
     * @param string         $className
     * @param string         $method
     * @param callable|array $callback
     * @param array          $params
     *
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     * @throws Throwable
     */
    public function run($target, string $className, string $method, $callback, $params = [])
    {
        if ($method == $this->fallback) {
            throw new BreakerException(sprintf('Method(%s) and fallback must be different', $method));
        }

        try {

            // Check state
            $this->state->check();

            if ($this->timeout == 0) {
                $result = PhpHelper::call($callback);
                $this->state->success();

                return $result;
            }

            $channel = new Channel(1);
            sgo(function () use ($callback, $channel) {
                try {
                    $result = PhpHelper::call($callback);
                    $channel->push([true, $result]);
                } catch (Throwable $e) {
                    $message = sprintf('%s file=%s line=%d', $e->getMessage(), $e->getFile(), $e->getLine());
                    $channel->push([false, $message]);
                }
            }, false);

            $data = $channel->pop($this->timeout);
            if ($data === false) {
                throw new BreakerException(
                    sprintf('Breaker call timeout(%f)', $this->timeout)
                );
            }

            [$status, $result] = $data;
            if ($status == false) {
                throw new BreakerException($result);
            }

            $this->state->success();
            return $result;
        } catch (Throwable $e) {
            $message = sprintf(
                'Breaker(%s->%s %s) call fail!(%s)',
                $className,
                $method,
                json_encode($params),
                $e->getMessage()
            );

            Log::error($message);
            $this->state->exception();

            if (!empty($this->fallback)) {
                return PhpHelper::call([$target, $this->fallback], ...$params);
            }

            throw $e;
        }
    }
}