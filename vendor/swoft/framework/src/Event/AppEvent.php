<?php

namespace Swoft\Event;

/**
 * Application event
 */
class AppEvent
{
    /**
     * Application loader event
     */
    const APPLICATION_LOADER = 'applicationLoader';

    /**
     * Pipe message event
     */
    const PIPE_MESSAGE = 'pipeMessage';

    /**
     * Resource release event behind application
     */
    const RESOURCE_RELEASE = 'resourceRelease';

    /**
     * Before resource release
     */
    const RESOURCE_RELEASE_BEFORE = 'resourceReleaseBefore';

    /**
     * Worker start event
     */
    const WORKER_START = 'workerStart';
}
