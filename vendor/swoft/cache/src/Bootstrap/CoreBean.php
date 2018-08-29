<?php

namespace Swoft\Cache\Bootstrap;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Cache\Cache;
use Swoft\Core\BootBeanInterface;

/**
 * The core bean of cache
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
            'cache' => [
                'class' => Cache::class,
            ]
        ];
    }
}