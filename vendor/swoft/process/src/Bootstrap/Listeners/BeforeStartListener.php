<?php

namespace Swoft\Process\Bootstrap\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Process\Bean\Collector\ProcessCollector;
use Swoft\Process\ProcessBuilder;

/**
 * the listener of before server start
 *
 * @BeforeStart()
 */
class BeforeStartListener implements BeforeStartInterface
{
    /**
     * @param AbstractServer $server
     */
    public function onBeforeStart(AbstractServer $server)
    {
        // add process
        $this->addProcess($server);
    }

    /**
     * @param AbstractServer $server
     */
    private function addProcess(AbstractServer &$server)
    {
        $processes = ProcessCollector::getCollector();

        foreach ($processes as $beanName => $processInfo) {
            $num  = $processInfo['num'];
            $name = $processInfo['name'];
            $boot = $processInfo['boot'];

            $processObject = App::getBean($name);

            if (!$processObject->check() || !$boot) {
                continue;
            }

            while ($num > 0) {
                $num--;
                $process = ProcessBuilder::create($name);
                if ($process === null) {
                    continue;
                }

                $server->getServer()->addProcess($process->getProcess());
            }
        }
    }
}
