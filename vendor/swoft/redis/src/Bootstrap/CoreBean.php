<?php

namespace Swoft\Redis\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;
use Swoft\Redis\Redis;

/**
 * CoreBean
 *
 * @BootBean()
 */
class CoreBean implements BootBeanInterface
{
    public function beans()
    {
        return [
            Redis::class => [
                'class' => Redis::class
            ]
        ];
    }
}