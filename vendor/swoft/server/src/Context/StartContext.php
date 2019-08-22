<?php declare(strict_types=1);


namespace Swoft\Server\Context;


use ReflectionException;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server as SwooleServer;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class StartContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class StartContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var SwooleServer
     */
    private $server;

    /**
     * @param SwooleServer $server
     *
     * @return StartContext
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(SwooleServer $server): self
    {
        $self = self::__instance();

        $self->server = $server;

        return $self;
    }

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->server;
    }
}