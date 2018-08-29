<?php

namespace Swoft\Redis\Operator\Processor;

use Swoft\Redis\Operator\CommandInterface;

interface ProcessorInterface
{
    /**
     * Processes the given Redis command.
     *
     * @param CommandInterface $command Command instance.
     */
    public function process(CommandInterface $command);
}
