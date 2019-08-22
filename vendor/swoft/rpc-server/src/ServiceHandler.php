<?php declare(strict_types=1);


namespace Swoft\Rpc\Server;


use function array_splice;
use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;
use Swoft\Rpc\Server\Exception\RpcServerException;

/**
 * Class ServiceHandler
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class ServiceHandler implements RequestHandlerInterface
{
    use PrototypeTrait;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var string
     */
    protected $defaultMiddleware = '';

    /**
     * Current offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * @param array  $middlewares
     * @param string $defaultMiddleware
     *
     * @return self
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(array $middlewares, string $defaultMiddleware): self
    {
        $instance = self::__instance();

        $instance->offset = 0;

        $instance->middlewares       = $middlewares;
        $instance->defaultMiddleware = $defaultMiddleware;

        return $instance;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        // Default middleware to handle request route
        $middleware = $this->middlewares[$this->offset] ?? $this->defaultMiddleware;

        /* @var MiddlewareInterface $bean */
        $bean = BeanFactory::getBean($middleware);

        // Next middleware
        $this->offset++;

        return $bean->process($request, $this);
    }

    /**
     * Insert middleware at offset
     *
     * @param array    $middlewares
     * @param int|null $offset
     *
     * @throws RpcServerException
     */
    public function insertMiddlewares(array $middlewares, int $offset = null): void
    {
        $offset = $offset ?? $this->offset;
        if ($offset > $this->offset) {
            throw new RpcServerException('Insert middleware offset must more than ' . $this->offset);
        }

        // Insert middlewares
        array_splice($this->middlewares, $offset, 0, $middlewares);
    }
}