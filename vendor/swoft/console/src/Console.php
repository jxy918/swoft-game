<?php

namespace Swoft\Console;

use Swoft\App;
use Swoft\Console\Bean\Collector\CommandCollector;
use Swoft\Core\Coroutine;

/**
 * Console
 */
class Console implements ConsoleInterface
{
    /**
     * Console constructor.
     */
    public function __construct()
    {
        $this->registerMapping();
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            /* @var \Swoft\Console\Command $command */
            $command = App::getBean('command');
            $command->run();
        } catch (\Throwable $e) {
            \output()->writeln(sprintf('<error>%s</error>', $e->getMessage()), true, false);
            \output()->writeln(sprintf("Trace:\n%s", $e->getTraceAsString()), true, true);
        }
    }

    /**
     * Register mapping
     *
     * @return void
     */
    private function registerMapping()
    {
        /* @var \Swoft\Console\Router\HandlerMapping $route */
        $route = App::getBean('commandRoute');

        $commandMapping = CommandCollector::getCollector();
        $route->register($commandMapping);
    }
}
