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

use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Stdlib\Helper\PhpHelper;

/**
 * Class LazyListener - Wrap callable into an object
 *
 * @package Swoft\Event\Listener
 * @since   2.0
 */
class LazyListener implements EventHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     *
     * @return LazyListener
     */
    public static function create(callable $callback): self
    {
        return new self($callback);
    }

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        PhpHelper::call($this->callback, $event);
    }

    /**
     * @return callable|mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
