<?php

namespace Swoft\Rpc\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;
use Swoft\Rpc\Packer\ServicePacker;

/**
 * The core bean of rpc
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
            'servicePacker'     => [
                'class'   => ServicePacker::class,
            ]
        ];
    }
}