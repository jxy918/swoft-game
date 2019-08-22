<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

use Swoft\WebSocket\Server\Message\Message;

/**
 * Interface MessageParserInterface
 *
 * @since   2.0
 * @package Swoft\WebSocket\Server\Contract
 */
interface MessageParserInterface
{
    /**
     * Encode message data to string.
     *
     * @param Message $message
     *
     * @return string
     */
    public function encode(Message $message): string;

    /**
     * Decode swoole Frame to Message object
     *
     * @param string $data Message data
     *
     * @return Message
     */
    public function decode(string $data): Message;
}
