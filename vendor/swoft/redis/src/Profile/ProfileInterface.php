<?php

namespace Swoft\Redis\Profile;

use Swoft\Redis\Operator\CommandInterface;

interface ProfileInterface
{
    /**
     * Checks if the profile supports the specified command.
     *
     * @param string $commandID Command ID.
     *
     * @return bool
     */
    public function supportsCommand(string $commandID): bool;

    /**
     * Creates a new command instance.
     *
     * @param string $commandID Command ID.
     * @param array  $arguments Arguments for the command.
     *
     * @return CommandInterface
     */
    public function createCommand(string $commandID, array $arguments = []): CommandInterface;
}
