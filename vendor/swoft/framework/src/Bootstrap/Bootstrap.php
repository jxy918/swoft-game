<?php

namespace Swoft\Bootstrap;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\BootstrapCollector;
use Swoft\Bootstrap\Boots\Bootable;

/**
 * Bootstrap
 *
 * @Bean()
 */
class Bootstrap implements Bootable
{
    /**
     * bootstrap
     */
    public function bootstrap()
    {
        $bootstraps = BootstrapCollector::getCollector();
        $temp = \array_column($bootstraps, 'order');

        \array_multisort($temp, SORT_ASC, $bootstraps);

        foreach ($bootstraps as $bootstrapBeanName => $name){
            /* @var Bootable $bootstrap*/
            $bootstrap = App::getBean($bootstrapBeanName);
            $bootstrap->bootstrap();
        }
    }
}
