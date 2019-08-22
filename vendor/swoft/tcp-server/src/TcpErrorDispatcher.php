<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\ErrorManager;
use Swoft\Error\ErrorType;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;
use Swoft\Tcp\Server\Contract\TcpConnectErrorHandlerInterface;
use Swoft\Tcp\Server\Contract\TcpReceiveErrorHandlerInterface;
use Throwable;

/**
 * Class TcpErrorDispatcher
 *
 * @since 2.0.3
 * @Bean()
 */
class TcpErrorDispatcher
{
    /**
     * @param Throwable $e
     * @param int       $fd
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function connectError(Throwable $e, int $fd): void
    {
        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var TcpConnectErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::TCP_CNT)) {
            $handler->handle($e, $fd);
            return;
        }

        $this->logError('Connect', $e);
    }

    /**
     * @param Throwable $e
     * @param Response  $response
     *
     * @return Response
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function receiveError(Throwable $e, Response $response): Response
    {
        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var TcpReceiveErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::TCP_RCV)) {
            return $handler->handle($e, $response);
        }

        $this->logError('Receive', $e);

        $response->setCode($e->getCode());
        $response->setMsg($e->getMessage());

        return $response;
    }

    /**
     * @param Throwable $e
     * @param int       $fd
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function closeError(Throwable $e, int $fd): void
    {
        /** @var ErrorManager $handlers */
        $handlers = Swoft::getSingleton(ErrorManager::class);

        /** @var Swoft\Tcp\Server\Contract\TcpCloseErrorHandlerInterface $handler */
        if ($handler = $handlers->matchHandler($e, ErrorType::TCP_CLS)) {
            $handler->handle($e, $fd);
            return;
        }

        $this->logError('Close', $e);
    }

    /**
     * @param string    $typeName
     * @param Throwable $e
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    private function logError(string $typeName, Throwable $e): void
    {
        Log::error($e->getMessage());
        CLog::error("Tcp %s Error(no handler, %s): %s\nAt File %s line %d\nTrace:\n%s",
            $typeName,
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
    }
}
