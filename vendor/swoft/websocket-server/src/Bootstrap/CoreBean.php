<?php

namespace Swoft\WebSocket\Server\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;
use Swoft\WebSocket\Server\Router\HandlerMapping;
use Swoft\WebSocket\Server\Router\Dispatcher;

/**
 * The core bean of service
 *
 * @BootBean()
 */
class CoreBean implements BootBeanInterface
{
    /**
     * @return array
     */
    public function beans(): array
    {
        return [
            'wsDispatcher' => [
                'class' => Dispatcher::class,
            ],
            'wsRouter'     => [
                'class' => HandlerMapping::class,
            ],
        ];
    }
}
