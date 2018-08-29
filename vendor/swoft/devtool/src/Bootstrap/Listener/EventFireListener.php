<?php

namespace Swoft\Devtool\Bootstrap\Listener;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\Annotation\Value;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;

/**
 * Class EventFireListener
 * @package Swoft\Devtool\Bootstrap\Listener
 * @Listener("*")
 */
class EventFireListener implements EventHandlerInterface
{
    /**
     * @Value("${config.devtool.logEventToConsole}")
     * @var bool
     */
    public $logEventToConsole = false;

    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        if (!$this->logEventToConsole) {
            return;
        }

        ConsoleUtil::log(
            \sprintf('Trigger the event <cyan>%s</cyan>', $event->getName()),
            [],
            'debug',
            [
                'Application',
                'WorkerId' => App::getWorkerId()
            ]
        );
    }
}
