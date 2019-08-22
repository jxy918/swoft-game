<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Helper;

use Swoole\Http\Request;
use Swoole\Http\Response;
use function base64_decode;
use function base64_encode;
use function preg_match;
use function sha1;
use function strlen;
use function trim;

/**
 * Class WsHelper
 *
 * @since   2.0
 * @package Swoft\WebSocket\Server\Helper
 */
class WsHelper
{
    public const WS_VERSION = '13';
    public const KEY_PATTEN = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
    public const SIGN_KEY   = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    /**
     * WebSocket data opcode types
     *
     * @see \WEBSOCKET_OPCODE_TEXT
     */
    public const OPCODE_TEXT   = 0x01;
    public const OPCODE_BINARY = 0x02;
    public const OPCODE_CLOSE  = 0x08;
    public const OPCODE_PING   = 0x09;
    public const OPCODE_PONG   = 0x10;

    /**
     * Generate WebSocket sign.(for server)
     *
     * @param string $key
     *
     * @return string
     */
    public static function genSign(string $key): string
    {
        return base64_encode(sha1(trim($key) . self::SIGN_KEY, true));
    }

    /**
     * @param string $secWSKey 'sec-websocket-key: xxxx'
     *
     * @return bool
     */
    public static function isInvalidSecKey(string $secWSKey): bool
    {
        return 0 === preg_match(self::KEY_PATTEN, $secWSKey)
            || 16 !== strlen(base64_decode($secWSKey));
    }

    /**
     * @param string $secWSKey
     *
     * @return array
     */
    public static function handshakeHeaders(string $secWSKey): array
    {
        return [
            'Upgrade'               => 'websocket',
            'Connection'            => 'Upgrade',
            'Sec-WebSocket-Accept'  => self::genSign($secWSKey),
            'Sec-WebSocket-Version' => self::WS_VERSION,
        ];
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    public static function fastHandshake(Request $request, Response $response): bool
    {
        // $this->log("received handshake request from fd #{$request->fd}, co ID #" . Coroutine::tid());
        // WebSocket握手连接算法验证
        $secKey = $request->header['sec-websocket-key'];
        if (self::isInvalidSecKey($secKey)) {
            $response->end();
            return false;
        }

        $headers = self::handshakeHeaders($secKey);

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();
        return true;
    }
}
