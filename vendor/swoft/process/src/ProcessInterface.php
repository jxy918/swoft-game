<?php

namespace Swoft\Process;

use Swoft\Process\Process as SwoftProcess;

/**
 * The process interface
 */
interface ProcessInterface
{
    /**
     * @param SwoftProcess $process
     */
    public function run(SwoftProcess $process);

    /**
     * @return bool
     */
    public function check(): bool;
}
