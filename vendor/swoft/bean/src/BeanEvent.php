<?php declare(strict_types=1);


namespace Swoft\Bean;

/**
 * Class BeanEvent
 *
 * @since 2.0
 */
class BeanEvent
{
    /**
     * Destroy request bean event
     */
    public const DESTROY_REQUEST = 'swoft.bean.request.destroy';

    /**
     * Destroy session event
     */
    public const DESTROY_SESSION = 'swoft.bean.session.destroy';
}