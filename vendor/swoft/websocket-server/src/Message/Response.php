<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Message;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Exception\SwoftException;
use Swoft\Server\Concern\CommonProtocolDataTrait;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\Connection;
use Swoft\WebSocket\Server\Context\WsMessageContext;
use Swoft\WebSocket\Server\Contract\ResponseInterface;
use function bean;
use function is_object;
use const WEBSOCKET_OPCODE_TEXT;

/**
 * Class Response
 *
 * @since 2.0.1
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Response implements ResponseInterface
{
    use CommonProtocolDataTrait;

    /**
     * Receiver fd
     *
     * @var int
     */
    private $fd = -1;

    /**
     * Receiver fd list
     *
     * @var array
     */
    private $fds = [];

    /**
     * Exclude fd list
     *
     * @var array
     */
    private $noFds = [];

    /**
     * @var bool
     */
    private $sent = false;

    /**
     * Sender fd
     *
     * @var int
     */
    private $sender = -1;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var bool
     */
    private $sendToAll = false;

    /**
     * @var bool
     */
    private $finish = true;

    /**
     * @var int WebSocket opcode value
     *          text:   WEBSOCKET_OPCODE_TEXT   = 1
     *          binary: WEBSOCKET_OPCODE_BINARY = 2
     *          close:  WEBSOCKET_OPCODE_CLOSE  = 8
     *          ping:   WEBSOCKET_OPCODE_PING   = 9
     *          pong:   WEBSOCKET_OPCODE_PONG   = 10
     */
    private $opcode = WEBSOCKET_OPCODE_TEXT;

    /**
     * @var int
     */
    private $pageSize = 50;

    /**
     * @param int $sender
     *
     * @return Response
     */
    public static function new(int $sender = -1): self
    {
        $self = bean(self::class);

        // Set properties
        $self->sent      = false;
        $self->sender    = $sender;
        $self->sendToAll = false;

        return $self;
    }

    /**
     * @param int $sender Sender fd
     *
     * @return $this
     */
    public function from(int $sender): ResponseInterface
    {
        return $this->setSender($sender);
    }

    /**
     * @param int $fd
     *
     * @return Response
     */
    public function to(int $fd): ResponseInterface
    {
        return $this->setFd($fd);
    }

    /**
     * @param int $fd
     *
     * @return Response
     */
    public function toOne(int $fd): self
    {
        return $this->setFd($fd);
    }

    /**
     * @param int[] $fd
     *
     * @return Response
     */
    public function toMore(array $fd): ResponseInterface
    {
        return $this->toSome($fd);
    }

    /**
     * @param int[] $fds
     *
     * @return Response
     */
    public function toSome(array $fds): ResponseInterface
    {
        $this->fds = $fds;
        return $this;
    }

    /**
     * @param bool $yes
     *
     * @return Response
     */
    public function toAll(bool $yes = true): ResponseInterface
    {
        $this->sendToAll = $yes;
        return $this;
    }

    /**
     * @param Connection $conn
     *
     * @return int
     * @throws SwoftException
     */
    public function send(Connection $conn = null): int
    {
        // Deny repeat send.
        // But if you want send again, you can call `setSent(false)` before send.
        if ($this->sent) {
            return 0;
        }

        $server = bean('wsServer');

        $pageSize = $this->pageSize;
        $content  = $this->formatContent($conn);

        // To all users
        if ($this->sendToAll) {
            return $server->sendToAll($content, $this->sender, $pageSize, $this->opcode);
        }

        // To some users
        if ($this->fds) {
            return $server->sendToSome($content, $this->fds, $this->noFds, $this->sender, $pageSize, $this->opcode);
        }

        // To one user
        $ok = $server->sendTo($this->fd, $content, $this->sender, $this->opcode, $this->finish);

        return $ok ? 1 : 0;
    }

    /**
     * @param Connection|null $conn
     *
     * @return string
     * @throws SwoftException
     */
    protected function formatContent(Connection $conn = null): string
    {
        // Content for response
        $content = $this->content;

        if ($content === '') {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $conn = $conn ?: Session::mustGet();

            /** @var WsMessageContext $context */
            $context = Context::get(true);
            $parser  = $conn->getParser();

            if (is_object($this->data) && $this->data instanceof Message) {
                $message = $this->data;
            } else {
                $cmdId   = $context->getMessage()->getCmd();
                $message = Message::new($cmdId, $this->data, $this->ext);
            }

            $content = $parser->encode($message);
        }

        return $content;
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
     *
     * @return self
     */
    public function setFd(int $fd): ResponseInterface
    {
        $this->fd = $fd;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpcode(): int
    {
        return $this->opcode;
    }

    /**
     * @param int $opcode
     *
     * @return self
     */
    public function setOpcode(int $opcode): ResponseInterface
    {
        if ($opcode > 0 && $opcode < 11) {
            $this->opcode = $opcode;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isFinish(): bool
    {
        return $this->finish;
    }

    /**
     * @param bool $finish
     *
     * @return self
     */
    public function setFinish(bool $finish): ResponseInterface
    {
        $this->finish = $finish;
        return $this;
    }

    /**
     * @return int
     */
    public function getSender(): int
    {
        return $this->sender;
    }

    /**
     * @param int $sender
     *
     * @return Response|self
     */
    public function setSender(int $sender): ResponseInterface
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Response|ResponseInterface
     */
    public function setContent(string $content): ResponseInterface
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return ResponseInterface|self
     */
    public function setData($data): ResponseInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return Response
     */
    public function setPageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return array
     */
    public function getNoFds(): array
    {
        return $this->noFds;
    }

    /**
     * @param array $noFds
     *
     * @return Response
     */
    public function setNoFds(array $noFds): self
    {
        $this->noFds = $noFds;
        return $this;
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
     *
     * @return Response
     */
    public function setSent(bool $sent): self
    {
        $this->sent = $sent;
        return $this;
    }
}
