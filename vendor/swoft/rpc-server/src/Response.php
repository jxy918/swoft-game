<?php declare(strict_types=1);


namespace Swoft\Rpc\Server;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Error;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Packet;
use Swoft\Rpc\Server\Contract\ResponseInterface;
use Swoole\Server;

/**
 * Class Response
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Response implements ResponseInterface
{
    use PrototypeTrait;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var int
     */
    protected $fd = 0;

    /**
     * @var int
     */
    protected $reactorId = 0;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var Error
     */
    protected $error;

    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     *
     * @return Response
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(Server $server = null, int $fd = null, int $reactorId = null): self
    {
        $instance = self::__instance();

        $instance->server    = $server;
        $instance->reactorId = $reactorId;
        $instance->fd        = $fd;

        return $instance;
    }

    /**
     * @param Error $error
     *
     * @return ResponseInterface
     */
    public function setError(Error $error): ResponseInterface
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param $data
     *
     * @return ResponseInterface
     */
    public function setData($data): ResponseInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $content
     *
     * @return ResponseInterface
     */
    public function setContent(string $content): ResponseInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcException
     */
    public function send(): bool
    {
        $this->prepare();
        return $this->server->send($this->fd, $this->content);
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcException
     */
    protected function prepare(): void
    {
        /* @var Packet $packet */
        $packet = \bean('rpcServerPacket');

        if ($this->error === null) {
            $this->content = $packet->encodeResponse($this->data);
            return;
        }

        $code    = $this->error->getCode();
        $message = $this->error->getMessage();
        $data    = $this->error->getData();

        $this->content = $packet->encodeResponse(null, $code, $message, $data);
    }
}