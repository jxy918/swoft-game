<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Context;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\AbstractContext;
use Swoft\WebSocket\Server\Message\Message;
use Swoft\WebSocket\Server\Message\Request;
use Swoft\WebSocket\Server\Message\Response;
use Swoole\WebSocket\Frame;

/**
 * Class WsMessageContext - on ws message event
 *
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class WsMessageContext extends AbstractContext
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return WsMessageContext
     */
    public static function new(Request $request, Response $response): self
    {
        /** @var self $ctx */
        $ctx = Swoft::getBean(self::class);

        // Initial properties
        $ctx->request  = $request;
        $ctx->response = $response;

        return $ctx;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->request->getFd();
    }

    /**
     * @return Frame
     */
    public function getFrame(): Frame
    {
        return $this->request->getFrame();
    }

    /**
     * Clear resource
     */
    public function clear(): void
    {
        parent::clear();

        $this->request  = null;
        $this->response = null;
    }

    /**
     * Get message object.
     * Notice: Available only during the messaging phase
     *
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->request->getMessage();
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
