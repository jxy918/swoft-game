<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use Psr\Http\Message\ServerRequestInterface;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Concern\DataPropertyTrait;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Session\SessionInterface;
use Swoft\WebSocket\Server\Contract\MessageParserInterface;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\MessageParser\RawTextParser;
use Swoft\WebSocket\Server\Router\Router;
use function microtime;
use function server;
use const WEBSOCKET_OPCODE_TEXT;

/**
 * Class Connection
 *
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Connection implements SessionInterface
{
    use DataPropertyTrait;

    private const METADATA_KEY = 'metadata';

    /**
     * @var int
     */
    private $fd = 0;

    /**
     * Save handshake success module instance
     *
     * @var WsModuleInterface
     */
    // private $module;

    /**
     * @var Request|ServerRequestInterface
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var bool
     */
    private $handshake = false;

    /**
     * @var array
     * @see Router::$modules for fileds information
     */
    private $moduleInfo;

    /**
     * @param int      $fd
     * @param Request  $request
     * @param Response $response
     *
     * @return Connection
     */
    public static function new(int $fd, Request $request, Response $response): self
    {
        /** @var self $sess */
        $sess = Swoft::getBean(self::class);

        $sess->fd = $fd;

        // Init meta info
        $sess->buildMetadata($fd, $request->getUriPath());

        $sess->request   = $request;
        $sess->response  = $response;
        $sess->handshake = false;

        return $sess;
    }

    /**
     * @param int    $fd
     * @param string $path
     */
    private function buildMetadata(int $fd, string $path): void
    {
        $info = server()->getClientInfo($fd);

        server()->log("Handshake: conn#{$fd} send handshake request to {$path}, client info: ", $info, 'debug');

        $this->set(self::METADATA_KEY, [
            'fd'            => $fd,
            'ip'            => $info['remote_ip'],
            'port'          => $info['remote_port'],
            'path'          => $path,
            'connectTime'   => $info['connect_time'],
            'handshakeTime' => microtime(true),
        ]);
    }

    /**
     * @param string $data
     * @param int    $opcode
     * @param bool   $finish
     *
     * @return bool
     */
    public function push(string $data, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true): bool
    {
        return server()->push($this->fd, $data, $opcode, $finish);
    }

    /**
     * Clear resource
     */
    public function clear(): void
    {
        $this->data = [];
        // Clear data
        $this->request    = null;
        $this->response   = null;
        $this->handshake  = false;
        $this->moduleInfo = [];
    }

    /**
     * @return bool
     */
    public function isHandshake(): bool
    {
        return $this->handshake;
    }

    /**
     * @param bool $ok
     */
    public function setHandshake(bool $ok): void
    {
        $this->handshake = $ok;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->get(self::METADATA_KEY, []);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getMetaValue(string $key)
    {
        $data = $this->get(self::METADATA_KEY, []);

        return $data[$key] ?? null;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return MessageParserInterface
     */
    public function getParser(): MessageParserInterface
    {
        $parseClass = $this->getParserClass();

        return Swoft::getSingleton($parseClass);
    }

    /**
     * @return string
     */
    public function getParserClass(): string
    {
        return $this->moduleInfo['messageParser'] ?? RawTextParser::class;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getModuleInfo(): array
    {
        return $this->moduleInfo;
    }

    /**
     * @param array $moduleInfo
     */
    public function setModuleInfo(array $moduleInfo): void
    {
        $this->moduleInfo = $moduleInfo;
    }
}
