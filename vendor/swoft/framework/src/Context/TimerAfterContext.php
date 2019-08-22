<?php declare(strict_types=1);


namespace Swoft\Context;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class TimerAfterContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class TimerAfterContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var int
     */
    private $timerId = 0;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param int   $timerId
     * @param array $params
     *
     * @return TimerAfterContext
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(int $timerId, array $params): self
    {
        $self          = self::__instance();
        $self->timerId = $timerId;
        $self->params  = $params;

        return $self;
    }

    /**
     * @return int
     */
    public function getTimerId(): int
    {
        return $this->timerId;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}