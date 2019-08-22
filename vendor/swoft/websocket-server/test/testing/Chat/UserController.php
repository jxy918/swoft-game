<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Testing\Chat;

use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;
use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use SwoftTest\WebSocket\Server\Testing\ChatModule;

/**
 * Class UserController
 * @WsController(prefix="user", module=ChatModule::class)
 */
class UserController
{
    /**
     * @MessageMapping("login")
     */
    public function login(): void
    {

    }

    /**
     * @MessageMapping()
     */
    public function logout(): void
    {

    }
}
