<?php

namespace Swoft\Console;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Console\Router\HandlerMapping;
use Swoft\Core\BootBeanInterface;

/**
 * The core bean of console
 *
 * @BootBean(server=true)
 */
class CoreBean implements BootBeanInterface
{
    public function beans()
    {
        return [
            'commandRoute' => [
                'class' => HandlerMapping::class,
            ],
        ];
    }
}