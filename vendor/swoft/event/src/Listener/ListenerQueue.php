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

use Closure;
use Countable;
use IteratorAggregate;
use SplObjectStorage;
use SplPriorityQueue;
use stdClass;
use Traversable;
use function count;
use function is_object;
use const PHP_INT_MAX;

/**
 * Class ListenerQueue - Listener queue management class for an event
 *
 * @package Swoft\Event\Listener
 * @since   2.0
 */
class ListenerQueue implements IteratorAggregate, Countable
{
    /**
     * Object store - listener instance store
     *
     * @var SplObjectStorage
     */
    private $store;

    /**
     * Priority queue
     *
     * @var SplPriorityQueue
     */
    private $queue;

    /**
     * 计数器。设定最大值为 PHP_INT_MAX
     *
     * @var int
     */
    private $counter = PHP_INT_MAX;

    public function __construct()
    {
        $this->store = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Add a listener, support add callback(string|array)
     *
     * @param Closure|callable|stdClass|mixed $listener
     * @param integer                         $priority
     *
     * @return $this
     */
    public function add($listener, $priority): self
    {
        // transfer to object. like string/array
        if (!is_object($listener)) {
            $listener = new LazyListener($listener);
        }

        if (!$this->has($listener)) {
            // Compute the internal priority as an array. 计算内部优先级为一个数组。
            // @see http://php.net/manual/zh/splpriorityqueue.compare.php#93999
            $priorityData = [(int)$priority, $this->counter--];

            $this->store->attach($listener, $priorityData);
            $this->queue->insert($listener, $priorityData);
        }

        return $this;
    }

    /**
     * Delete a listener
     *
     * @param $listener
     *
     * @return $this
     */
    public function remove($listener): self
    {
        if ($this->has($listener)) {
            $this->store->detach($listener);
            $this->store->rewind();

            $queue = new SplPriorityQueue();

            foreach ($this->store as $otherListener) {
                // Priority information @see self::add(). It like `[priority, counter value]`
                $priority = $this->store->getInfo();
                $queue->insert($otherListener, $priority);
            }

            $this->queue = $queue;
        }

        return $this;
    }

    /**
     * Get the priority of the given listener. 得到指定监听器的优先级
     *
     * @param mixed $listener The listener.
     * @param int   $default  The default value to return if the listener doesn't exist.
     *
     * @return  int|null  The listener priority if it exists, null otherwise.
     */
    public function getPriority($listener, int $default = null): ?int
    {
        if ($this->store->contains($listener)) {
            // @see self::add(). attach as: `[priority, counter value]`
            return $this->store[$listener][0];
        }

        return $default;
    }

    /**
     * getPriority() alias method
     *
     * @param mixed $listener
     * @param int   $default
     *
     * @return mixed
     */
    public function getLevel($listener, int $default = null)
    {
        return $this->getPriority($listener, $default);
    }

    /**
     * Get all listeners contained in this queue, sorted according to their priority.
     *
     * @return  mixed[]  An array of listeners.
     */
    public function getAll(): array
    {
        $listeners = [];

        // Get a clone of the queue.
        $queue = $this->getIterator();

        foreach ($queue as $listener) {
            $listeners[] = $listener;
        }

        unset($queue);
        return $listeners;
    }

    /**
     * @param $listener
     *
     * @return bool
     */
    public function has($listener): bool
    {
        return $this->store->contains($listener);
    }

    /**
     * @param $listener
     *
     * @return bool
     */
    public function exists($listener): bool
    {
        return $this->has($listener);
    }

    /**
     * Get the inner queue with its cursor on top of the heap.
     *
     * @return  SplPriorityQueue  The inner queue.
     */
    public function getIterator(): Traversable
    {
        // SplPriorityQueue queue is a heap.
        $queue = clone $this->queue;

        // rewind pointer.
        if (!$queue->isEmpty()) {
            $queue->top();
        }

        return $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->queue);
    }

    /**
     * clear queue
     */
    public function clear(): void
    {
        $this->queue = null;
        $this->store->removeAll($this->store);
    }
}
