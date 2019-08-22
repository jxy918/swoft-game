<?php declare(strict_types=1);


namespace Swoft\Http\Server;

use function array_splice;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Container;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Server\Exception\HttpServerException;

/**
 * Class RequestHandler
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class RequestHandler implements RequestHandlerInterface
{
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
     * Initialize
     *
     * @param array  $middlewares
     * @param string $defaultMiddleware
     */
    public function initialize(array $middlewares, string $defaultMiddleware): void
    {
        $this->offset = 0;
        // init
        $this->middlewares       = $middlewares;
        $this->defaultMiddleware = $defaultMiddleware;
    }

    /**
     * Handler request by middleware
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Default middleware to handle request route
        $middleware = $this->middlewares[$this->offset] ?? $this->defaultMiddleware;

        /* @var MiddlewareInterface $bean */
        $bean = Container::$instance->getSingleton($middleware);

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
     * @throws HttpServerException
     */
    public function insertMiddlewares(array $middlewares, int $offset = null): void
    {
        $offset = $offset ?? $this->offset;
        if ($offset > $this->offset) {
            throw new HttpServerException('Insert middleware offset must more than ' . $this->offset);
        }

        // Insert middlewares
        array_splice($this->middlewares, $offset, 0, $middlewares);
    }
}
