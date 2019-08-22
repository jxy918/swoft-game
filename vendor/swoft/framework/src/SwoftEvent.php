<?php declare(strict_types=1);

namespace Swoft;

/**
 * Class SwoftEvent
 * @since 2.0
 */
final class SwoftEvent
{
    /**
     * Swoft init complete
     */
    public const APP_INIT_COMPLETE = 'swoft.init.complete';

    /**
     * Session complete
     *  - webSocket connection close
     *  - tcp connection close
     */
    public const SESSION_COMPLETE = 'swoft.session.complete';

    /**
     * Coroutine complete
     */
    public const COROUTINE_COMPLETE = 'swoft.co.complete';

    /**
     * Coroutine exception
     */
    public const COROUTINE_EXCEPTION = 'swoft.co.exception';

    /**
     * Coroutine destroy
     */
    public const COROUTINE_DESTROY = 'swoft.co.destroy';

    /**
     * Coroutine defer
     */
    public const COROUTINE_DEFER = 'swoft.co.defer';

    /**
     * Worker shutdown
     */
    public const WORKER_SHUTDOWN = 'swoft.worker.shutdown';

    /**
     * Timer after before
     */
    public const TIMER_AFTER_BEFORE = 'swoft.timer.after.before';

    /**
     * Timer after after
     */
    public const TIMER_AFTER_AFTER = 'swoft.timer.after.after';

    /**
     * Timer tick before
     */
    public const TIMER_TICK_BEFORE = 'swoft.timer.tick.before';

    /**
     * Timer tick after
     */
    public const TIMER_TICK_AFTER = 'swoft.timer.tick.after';
}
