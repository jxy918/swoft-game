<?php declare(strict_types=1);


namespace Swoft\Rpc;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class Error
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Error
{
    use PrototypeTrait;

    /**
     * @var int
     */
    private $code = 0;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param int    $code
     * @param string $message
     * @param mixed  $data
     *
     * @return Error
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(int $code, string $message, $data): self
    {
        $instance = self::__instance();

        $instance->code    = $code;
        $instance->message = $message;
        $instance->data    = $data;

        return $instance;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}