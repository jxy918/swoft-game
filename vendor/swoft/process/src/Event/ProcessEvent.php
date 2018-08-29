<?php

namespace Swoft\Process\Event;

/**
 * The process event
 */
class ProcessEvent
{
    /**
     * Before process
     */
    const BEFORE_PROCESS = "beforeProcess";

    /**
     * After process
     */
    const AFTER_PROCESS = "afterProcess";
}