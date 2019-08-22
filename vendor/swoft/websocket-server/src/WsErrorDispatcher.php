<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Error\DefaultErrorDispatcher;
use Swoft\Error\ErrorManager;
use Swoft\Error\ErrorType;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Log\Helper\CLog;
use Swoft\WebSocket\Server\Contract\CloseErrorHandlerInterface;
use Swoft\WebSocket\Server\Contract\HandshakeErrorHandlerInterface;
use Swoft\WebSocket\Server\Contract\MessageErrorHandlerInterface;
use Swoft\WebSocket\Server\Contract\OpenErrorHandlerInterface;
use Swoft\WebSocket\Server\Exception\WsMessageRouteException;
use Swoft\WebSocket\Server\Exception\WsModuleRouteException;
use Swoole\WebSocket\Frame;
use Throwable;
use function server;

/**
 * Class WsErrorDispatcher
 *
 * @since 2.0
 * @Bean()
 */
class WsErrorDispatcher
{
    /**
     * @param Throwable $e
     * @param Response  $response
     *
     * @return Response
     * @throws Throwable
     */
    public function handshakeError(Throwable $e, Response $response): Response
    {
        CLog::error('Websocket error: ' . $e->getMessage());

        // TODO should handle it?
        if ($e instanceof WsModuleRouteException) {
            return $response->withStatus(404)->withAddedHeader('Failure-Reason', 'Route not found');
        }

        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var HandshakeErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::WS_HS)) {
            return $handler->handle($e, $response);
        }

        // No handler, use default error dispatcher

        /** @var DefaultErrorDispatcher $defDispatcher */
        $defDispatcher = Swoft::getSingleton(DefaultErrorDispatcher::class);
        $defDispatcher->run($e);

        return $response->withStatus(500)->withContent($e->getMessage());
    }

    /**
     * @param Throwable $e
     * @param Request   $request
     *
     * @throws Throwable
     */
    public function openError(Throwable $e, Request $request): void
    {
        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var OpenErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::WS_OPN)) {
            $handler->handle($e, $request);
            return;
        }

        // No handler, use default error dispatcher

        /** @var DefaultErrorDispatcher $defDispatcher */
        $defDispatcher = Swoft::getSingleton(DefaultErrorDispatcher::class);
        $defDispatcher->run($e);
    }

    /**
     * @param Throwable $e
     * @param Frame     $frame
     *
     * @throws Throwable
     */
    public function messageError(Throwable $e, Frame $frame): void
    {
        if ($e instanceof WsMessageRouteException) {
            server()->push($frame->fd, $e->getMessage());
            return;
        }

        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var MessageErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::WS_MSG)) {
            $handler->handle($e, $frame);
            return;
        }

        // No handler, use default error dispatcher

        /** @var DefaultErrorDispatcher $defDispatcher */
        $defDispatcher = Swoft::getSingleton(DefaultErrorDispatcher::class);
        $defDispatcher->run($e);
    }

    /**
     * @param Throwable $e
     * @param int       $fd
     *
     * @throws Throwable
     */
    public function closeError(Throwable $e, int $fd): void
    {
        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var CloseErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::WS_CLS)) {
            $handler->handle($e, $fd);
            return;
        }

        // No handler, use default error dispatcher

        /** @var DefaultErrorDispatcher $defDispatcher */
        $defDispatcher = Swoft::getSingleton(DefaultErrorDispatcher::class);
        $defDispatcher->run($e);
    }
}
