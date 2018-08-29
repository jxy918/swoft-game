<?php

namespace Swoft\Devtool\Bootstrap;

use Swoft\App;
use Swoft\Bean\Annotation\BootBean;
use Swoft\Core\BootBeanInterface;

/**
 * The core bean
 *
 * @BootBean()
 */
class CoreBean implements BootBeanInterface
{
    /**
     * CoreBean constructor.
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        App::setAlias('@devtool', \dirname(__DIR__, 2));
    }

    /**
     * @return array
     */
    public function beans(): array
    {
        return [];
    }
}
