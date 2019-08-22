<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Context;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoft\Tcp\Server\Request;
use Swoft\Tcp\Server\Response;

/**
 * Class TcpReceiveContext
 *
 * @since 2.0.3
 * @Bean(scope=Bean::PROTOTYPE)
 */
class TcpReceiveContext extends AbstractContext
{
    /**
     * @var int
     */
    private $fd;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param int      $fd
     * @param Request  $request
     * @param Response $response
     *
     * @return self
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(int $fd, Request $request, Response $response): self
    {
        /** @var self $ctx */
        $ctx = Swoft::getBean(self::class);

        // Initial properties
        $ctx->fd = $fd;

        $ctx->request  = $request;
        $ctx->response = $response;

        return $ctx;
    }

    public function clear(): void
    {
        parent::clear();

        $this->request = null;
        $this->response = null;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
