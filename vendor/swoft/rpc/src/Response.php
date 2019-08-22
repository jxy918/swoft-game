<?php declare(strict_types=1);


namespace Swoft\Rpc;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class Response
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Response
{
    use PrototypeTrait;

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var Error|null
     */
    private $error;

    /**
     * @param $result
     * @param $error
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new($result, $error): self
    {
        $instance = self::__instance();

        $instance->result = $result;
        $instance->error  = $error;

        return $instance;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Error
     */
    public function getError(): ?Error
    {
        return $this->error;
    }
}