<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\MessageParser;

use function preg_match;
use function strlen;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\WebSocket\Server\Contract\MessageParserInterface;
use Swoft\WebSocket\Server\Message\Message;
use function explode;
use function strpos;
use function trim;

/**
 * Class TokenTextParser
 *
 * @since 2.0
 * @Bean()
 */
class TokenTextParser implements MessageParserInterface
{
    /**
     * Encode data to string.
     *
     * @param Message $message
     *
     * @return string
     */
    public function encode(Message $message): string
    {
        return $message->getDataString();
    }

    /**
     * Decode data to array
     *
     * @param string $data The raw message, use ':' to split cmd and data.
     * Format like:
     *  login:message body data
     * =>
     *  cmd: 'login'
     *  body: 'message body data'
     *
     * @return Message
     */
    public function decode(string $data): Message
    {
        $data = trim($data, ': ');

        // use default message command
        $cmd = '';
        if (strpos($data, ':') > 0) {
            [$cmd, $body] = explode(':', $data, 2);

            $cmd  = trim($cmd);
            $body = trim($body);

            // only an command
        } elseif (strlen($data) < 16 && 1 === preg_match('/^[\w-]+$/', $data)) {
            $cmd  = $data;
            $body = '';
        } else {
            $body = $data;
        }

        return Message::new($cmd, trim($body));
    }
}
