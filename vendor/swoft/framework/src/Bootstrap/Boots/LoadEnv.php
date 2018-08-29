<?php

namespace Swoft\Bootstrap\Boots;

use Dotenv\Dotenv;
use Swoft\App;
use Swoft\Bean\Annotation\Bootstrap;

/**
 * @Bootstrap(order=1)
 * @author    huangzhhui <huangzhwork@gmail.com>
 */
class LoadEnv implements Bootable
{
    /**
     * @throws \InvalidArgumentException
     */
    public function bootstrap()
    {
        $file = '.env';
        $filePath = App::getAlias('@root') . DS . $file;

        if (\file_exists($filePath) && \is_readable($filePath)) {
            (new Dotenv(App::getAlias('@root'), $file))->load();
        }
    }
}
