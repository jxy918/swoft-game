<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Event\Listener;

/**
 * Class ListenerPriority
 *
 * @package Swoft\Event\Listener
 * @since   2.0
 */
class ListenerPriority
{
    public const MIN          = -300;
    public const LOW          = -200;
    public const BELOW_NORMAL = -100;
    public const NORMAL       = 0;
    public const ABOVE_NORMAL = 100;
    public const HIGH         = 200;
    public const MAX          = 300;
}
