<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Testing\Chat;

use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use SwoftTest\WebSocket\Server\Testing\ChatModule;

/**
 * Class ChatController
 *
 * @WsController("chat", module=ChatModule::class)
 */
class ChatController
{
    /**
     * @MessageMapping()
     */
    public function send(): void
    {

    }

    /**
     * @MessageMapping()
     */
    public function notify(): void
    {

    }
}
