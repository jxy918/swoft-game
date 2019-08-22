<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\MessageParser;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\WebSocket\Server\Contract\MessageParserInterface;
use Swoft\WebSocket\Server\Message\Message;

/**
 * Class JsonParser
 *
 * @since 2.0
 * @Bean()
 */
class JsonParser implements MessageParserInterface
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function encode(Message $message): string
    {
        return JsonHelper::encode($message->toArray());
    }

    /**
     * Decode data to array.
     *
     * @param string $data Message data. It's {@see \Swoole\WebSocket\Frame::$data)
     *
     * @return Message
     */
    public function decode(string $data): Message
    {
        $cmd = '';
        $ext = [];
        $map = JsonHelper::decode($data, true);

        // Find message route command
        if (isset($map['cmd'])) {
            $cmd = (string)$map['cmd'];
            unset($map['cmd']);
        }

        if (isset($map['data'])) {
            $data = $map['data'];
            $ext  = $map['ext'] ?? [];
        } else {
            $data = $map;
        }

        return Message::new($cmd, $data, $ext);
    }
}
