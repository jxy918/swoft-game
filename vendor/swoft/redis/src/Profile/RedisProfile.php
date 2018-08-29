<?php

namespace Swoft\Redis\Profile;

use Swoft\Redis\Exception\RedisException;
use Swoft\Redis\Operator\CommandInterface;
use Swoft\Redis\Operator\Processor\PrefixProcessor;

abstract class RedisProfile implements ProfileInterface
{
    /**
     * SupportedCommands
     *
     * @var array
     */
    private $commands;

    /**
     * @var PrefixProcessor
     */
    protected $processor;

    /**
     * Init
     */
    public function init()
    {
        $this->commands = $this->getSupportedCommands();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCommand(string $commandID): bool
    {
        return isset($this->commands[strtoupper($commandID)]);
    }

    /**
     * {@inheritdoc}
     */
    public function createCommand(string $commandID, array $arguments = []): CommandInterface
    {
        $commandID = strtoupper($commandID);

        if (!isset($this->commands[$commandID])) {
            throw new RedisException("Command '{$commandID}' is not a registered Redis command.");
        }

        $commandClass = $this->commands[$commandID];

        /* @var CommandInterface $command*/
        $command      = new $commandClass();
        $command->setArguments($arguments);

        if (isset($this->processor)) {
            $this->processor->process($command);
        }

        return $command;
    }

    /**
     * Setter PrefixProcessor
     */
    public function setProcessor($processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * Getter PrefixProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Returns a map of all the commands supported by the profile and their
     * actual PHP classes.
     *
     * @return array
     */
    abstract protected function getSupportedCommands(): array;
}
