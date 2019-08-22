<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Log\Helper\CLog;
use Swoft\Tcp\Protocol;
use Swoft\Tcp\Server\Contract\ResponseInterface;
use Swoft\Tcp\Server\Exception\TcpResponseException;
use Swoft\Tcp\Response as TcpResponse;
use Swoole\Server;
use function bean;

/**
 * Class Response
 *
 * @since 2.0
 * @Bean(name="tcpResponse", scope=Bean::PROTOTYPE)
 */
class Response extends TcpResponse implements ResponseInterface
{
    /**
     * Response fd
     *
     * @var int
     */
    private $fd = -1;

    /**
     * Request fd
     *
     * @var int
     */
    private $reqFd = -1;

    /**
     * Mark whether the response has been sent
     *
     * @var bool
     */
    private $sent = false;

    /**
     * @param int $fd
     *
     * @return self|TcpResponse
     */
    public static function new(int $fd = -1): TcpResponse
    {
        /** @var self $self */
        $self = bean('tcpResponse');

        // Set properties
        $self->fd    = $fd;
        $self->sent  = false;
        $self->reqFd = $fd;

        return $self;
    }

    /**
     * @param Server|null $server
     *
     * @return int
     * @throws TcpResponseException
     */
    public function send(Server $server = null): int
    {
        if ($this->sent) {
            return 0;
        }

        /** @var Protocol $protocol */
        $protocol = bean('tcpServerProtocol');

        // Content is empty, skip send
        if (!$content = $protocol->packResponse($this)) {
            $this->sent = true;
            CLog::warning('cannot send empty content to tcp client');
            return 0;
        }

        $server = $server ?: Swoft::server()->getSwooleServer();
        if ($server->send($this->fd, $content) === false) {
            $code = $server->getLastError();
            throw new TcpResponseException("Error on send data to client #{$this->fd}", $code);
        }

        $this->sent = true;
        return 1;
    }

    /**
     * @return int
     */
    public function getReqFd(): int
    {
        return $this->reqFd;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @param int $fd
     */
    public function setFd(int $fd): void
    {
        $this->fd = $fd;
    }
}
