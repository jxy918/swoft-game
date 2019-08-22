<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Event;

/**
 * Interface EventInterface - Representation of an event
 *
 * @package Swoft\Event
 * @author  inhere <in.798@qq.com>
 * @link    https://github.com/php-fig/fig-standards/blob/master/proposed/event-manager.md
 */
interface EventInterface
{
    /**
     * Get event name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get target/context from which event was triggered
     *
     * @return null|string|object
     */
    public function getTarget();

    /**
     * Get parameters passed to the event
     *
     * @return array
     */
    public function getParams(): array;

    /**
     * Get a single parameter by name
     *
     * @param int|string $key
     * @param mixed      $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null);

    /**
     * Set the event name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void;

    /**
     * Set the event target
     *
     * @param null|string|object $target
     *
     * @return void
     */
    public function setTarget($target): void;

    /**
     * Set event parameters
     *
     * @param array $params
     *
     * @return void
     */
    public function setParams(array $params): void;

    /**
     * Indicate whether or not to stop propagating this event
     *
     * @param bool $flag
     */
    public function stopPropagation($flag);

    /**
     * Has this event indicated event propagation should stop?
     *
     * @return bool
     */
    public function isPropagationStopped(): bool;
}
