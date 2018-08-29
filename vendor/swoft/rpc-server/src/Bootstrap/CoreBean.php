<?php

namespace Swoft\Rpc\Server\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;
use Swoft\Rpc\Server\Router\HandlerMapping;
use Swoft\Rpc\Server\ServiceDispatcher;

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
    public function beans()
    {
        return [
            'ServiceDispatcher' => [
                'class' => ServiceDispatcher::class,
            ],
            'serviceRouter'     => [
                'class' => HandlerMapping::class,
            ],
        ];
    }
}