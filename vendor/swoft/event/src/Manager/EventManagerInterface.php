<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Event\Manager;

use Swoft\Event\EventInterface;

/**
 * Interface EventManagerInterface - Interface for EventManager
 *
 * @package Swoft\Event\Manager
 * @author  inhere <in.798@qq.com>
 */
interface EventManagerInterface
{
    /**
     * Attaches a listener to an event
     *
     * @param string   $event    the event to attach too
     * @param callable $callback a callable function
     * @param int      $priority the priority at which the $callback executed
     *
     * @return bool true on success false on failure
     */
    public function attach($event, $callback, $priority = 0): bool;

    /**
     * Detaches a listener from an event
     *
     * @param string   $event    the event to attach too
     * @param callable $callback a callable function
     *
     * @return bool true on success false on failure
     */
    public function detach($event, $callback): bool;

    /**
     * Clear all listeners for a given event
     *
     * @param string $event
     *
     * @return void
     */
    public function clearListeners($event): void;

    /**
     * Trigger an event
     *
     * Can accept an EventInterface or will create one if not passed
     *
     * @param string|EventInterface $event
     * @param object|string         $target
     * @param array|object          $argv
     *
     * @return mixed
     */
    public function trigger($event, $target = null, array $argv = []);
}
