<?php

namespace Swoft\View\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;
use Swoft\View\Base\View;

/**
 *  The core bean of view
 *
 * @BootBean()
 */
class CoreBean implements BootBeanInterface
{

    /**
     * @return array
     */
    public function beans():array
    {
        return [
            'view'         => [
                'class'     => View::class,
            ],
        ];
    }
}