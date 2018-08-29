<?php

namespace Swoft\Event;

/**
 * Class EventManager
 * @package Swoft\Event
 * @author    inhere <in.798@qq.com>
 */
class EventManager implements EventManagerInterface
{
    // 通配符 - 所有触发的事件都会流过
    const MATCH_ALL = '*';

    /**
     * @var self
     */
    private $parent;

    /**
     * @var EventInterface
     */
    private $basicEvent;

    /**
     * 预定义的事件存储
     * @var EventInterface[]
     * [
     *     'event name' => (object)EventInterface -- event description
     * ]
     */
    protected $events = [];

    /**
     * 监听器存储
     * @var ListenerQueue[]
     */
    protected $listeners = [];

    /*
     * 含有通配符的监听器存储 eg 'db.*'
     * - '*' 只有这一个字符时，所有的事件处理都会经过它
     * - 'db.*' 触发有 'db.' 前缀的事件也都会经过 'db.*' 的监听器处理
     * @var array
     */
    // protected $wildcards = [];

    /**
     * EventManager constructor.
     * @param EventManagerInterface|null $parent
     * @throws \InvalidArgumentException
     */
    public function __construct(EventManagerInterface $parent = null)
    {
        if ($parent) {
            $this->parent = $parent;
        }

        $this->basicEvent = new Event;
    }

    public function __destruct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->parent = $this->basicEvent = null;
        $this->events = $this->listeners = [];
    }

    /*******************************************************************************
     * Listener manager
     ******************************************************************************/

    /**
     * Attaches a listener to an event
     * @param string $event the event to attach too
     * @param callable|EventHandlerInterface|mixed $callback A callable listener
     * @param int $priority the priority at which the $callback executed
     * @return bool true on success false on failure
     */
    public function attach($event, $callback, $priority = 0): bool
    {
        return $this->addListener($callback, [$event => $priority]);
    }

    /**
     * Detaches a listener from an event
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @return bool true on success false on failure
     */
    public function detach($event, $callback): bool
    {
        return $this->removeListener($callback, $event);
    }

    /**
     * @param EventSubscriberInterface $eventSubscriber
     */
    public function addSubscriber(EventSubscriberInterface $eventSubscriber)
    {
        $this->addListener($eventSubscriber);
    }

    /**
     * 添加监听器 并关联到 某一个(多个)事件
     * @param \Closure|callback|mixed $listener 监听器
     * @param array|string|int $definition 事件名，优先级设置
     * Allowed:
     *     $definition = [
     *        'event name' => priority(int),
     *        'event name1' => priority(int),
     *     ]
     * OR
     *     $definition = [
     *        'event name','event name1',
     *     ]
     * OR
     *     $definition = 'event name'
     * OR
     *     // The priority of the listener 监听器的优先级
     *     $definition = 1
     * @return bool
     */
    public function addListener($listener, $definition = null): bool
    {
        // ensure $listener is a object.
        if (!\is_object($listener)) {
            if (\is_string($listener) && \class_exists($listener)) {
                $listener = new $listener;

                // like 'function' OR '[object, method]'
            } else {
                $listener = new LazyListener($listener);
            }
        }

        $defaultPriority = ListenerPriority::NORMAL;

        if (is_numeric($definition)) {
            $defaultPriority = (int)$definition;
            $definition = null;
        } elseif (\is_string($definition)) { // 仅是个 事件名称
            $definition = [$definition => $defaultPriority];
        } elseif ($definition instanceof EventInterface) { // 仅是个 事件对象,取出名称
            $definition = [$definition->getName() => $defaultPriority];
        }

        if ($listener instanceof EventSubscriberInterface) {
            foreach ($listener::getSubscribedEvents() as $name => $conf) {
                if (!isset($this->listeners[$name])) {
                    $this->listeners[$name] = new ListenerQueue;
                }

                $queue = $this->listeners[$name];

                if (\is_string($conf)) {
                    $queue->add(new LazyListener([$listener, $conf]), $defaultPriority);
                    // ['onPost', ListenerPriority::LOW]
                } elseif (\is_string($conf[0])) {
                    $queue->add(new LazyListener([$listener, $conf[0]]), $conf[1] ?? $defaultPriority);
                }
            }

            return true;
        }

        // 将 监听器 关联到 各个事件
        if ($definition) {
            foreach ($definition as $name => $priority) {
                if (\is_int($name)) {
                    if (!$priority || !\is_string($priority)) {
                        continue;
                    }

                    $name = $priority;
                    $priority = $defaultPriority;
                }

                if (!$name = trim($name, '. ')) {
                    continue;
                }

                if (!isset($this->listeners[$name])) {
                    $this->listeners[$name] = new ListenerQueue;
                }

                $this->listeners[$name]->add($listener, $priority);
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @throws \InvalidArgumentException
     */
    public function triggerEvent(EventInterface $event)
    {
        return $this->trigger($event);
    }

    /**
     * @param array $events
     * @param array $args
     * @return array
     * @throws \InvalidArgumentException
     */
    public function triggerBatch(array $events, array $args = []): array
    {
        $results = [];

        foreach ($events as $event) {
            $results[] = $this->trigger($event, null, $args);
        }

        return $results;
    }

    /**
     * Trigger an event
     * Can accept an EventInterface or will create one if not passed
     * @param  string|EventInterface $event 'app.start' 'app.stop'
     * @param  mixed|string $target It is object or string.
     * @param  array|mixed $args
     * @return EventInterface
     * @throws \InvalidArgumentException
     */
    public function trigger($event, $target = null, array $args = []): EventInterface
    {
        if (!$event instanceof EventInterface) {
            $event = (string)$event;

            if (isset($this->events[$event])) {
                $event = $this->events[$event];
            } else {
                $event = $this->wrapperEvent($event);
            }
        }

        /** @var EventInterface $event */
        if (!$name = $event->getName()) {
            throw new \InvalidArgumentException('The triggered event name cannot be empty!');
        }

        $event->setParams($args);
        $event->setTarget($target);

        // Initial value of stop propagation flag should be false
        $event->stopPropagation(false);

        // have matched listener
        if (isset($this->listeners[$name])) {
            $this->triggerListeners($this->listeners[$name], $event);

            if ($event->isPropagationStopped()) {
                return $event;
            }
        }

        // have matched listener in parent
        if ($this->parent && ($listenerQueue = $this->parent->getListenerQueue($event))) {
            $this->triggerListeners($listenerQueue, $event);
            unset($listenerQueue);
        }

        // like 'app.start' 'app.db.query'
        if ($pos = \strrpos($name, '.')) {
            $prefix = substr($name, 0, $pos);
            $method = substr($name, $pos + 1);

            // have a group listener. eg 'app'
            if (isset($this->listeners[$prefix])) {
                $this->triggerListeners($this->listeners[$prefix], $event, $method);
            }

            if ($event->isPropagationStopped()) {
                return $event;
            }

            // have a wildcards listener. eg 'app.*'
            $wildcardEvent = $prefix . '.*';

            if (isset($this->listeners[$wildcardEvent])) {
                $this->triggerListeners($this->listeners[$wildcardEvent], $event, $method);
            }

            if ($event->isPropagationStopped()) {
                return $event;
            }
        }

        // have global wildcards '*' listener.
        if (isset($this->listeners['*'])) {
            $this->triggerListeners($this->listeners['*'], $event);
        }

        return $event;
    }

    /**
     * @param array|ListenerQueue $listeners
     * @param EventInterface $event
     * @param null $method
     */
    protected function triggerListeners($listeners, EventInterface $event, $method = null)
    {
        // $handled = false;
        $name = $event->getName();
        $callable = false === \strpos($name, '.');

        // 循环调用监听器，处理事件
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            if (\is_object($listener)) {
                if ($listener instanceof EventHandlerInterface) {
                    $listener->handle($event);
                } elseif ($method && method_exists($listener, $method)) {
                    $listener->$method($event);
                } elseif ($callable && method_exists($listener, $name)) {
                    $listener->$name($event);
                } elseif (method_exists($listener, '__invoke')) {
                    $listener($event);
                }
            } elseif (\is_callable($listener)) {
                $listener($event);
            }
        }
    }

    // protected function collectListeners(EventInterface $event)
    // {
    //     // $name = $event->getName();
    // }

    /**
     * 是否存在 对事件的 监听队列
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListenerQueue($event): bool
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]);
    }

    /**
     * @see self::hasListenerQueue() alias method
     * @param  EventInterface|string $event
     * @return boolean
     */
    public function hasListeners($event): bool
    {
        return $this->hasListenerQueue($event);
    }

    /**
     * 是否存在(对事件的)监听器
     * @param $listener
     * @param  EventInterface|string $event
     * @return bool
     */
    public function hasListener($listener, $event = null): bool
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            if (isset($this->listeners[$event])) {
                return $this->listeners[$event]->has($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                if ($queue->has($listener)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 获取事件的一个监听器的优先级别
     * @param $listener
     * @param  string|EventInterface $event
     * @return int|null
     */
    public function getListenerPriority($listener, $event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getPriority($listener);
        }

        return null;
    }

    /**
     * 获取事件的所有监听器
     * @param  string|EventInterface $event
     * @return ListenerQueue|null
     */
    public function getListenerQueue($event)
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event];
        }

        return null;
    }

    /**
     * 获取事件的所有监听器
     * @param  string|EventInterface $event
     * @return array
     */
    public function getListeners($event): array
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->listeners[$event])) {
            return $this->listeners[$event]->getAll();
        }

        return [];
    }

    /**
     * 统计获取事件的监听器数量
     * @param  string|EventInterface $event
     * @return int
     */
    public function countListeners($event): int
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]) ? \count($this->listeners[$event]) : 0;
    }

    /**
     * 移除对某个事件的监听
     * @param $listener
     * @param null|string|EventInterface $event
     * 为空时，移除监听者队列中所有名为 $listener 的监听者
     * 否则， 则移除对事件 $event 的监听者
     * @return bool
     */
    public function removeListener($listener, $event = null): bool
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                $this->listeners[$event]->remove($listener);
            }
        } else {
            foreach ($this->listeners as $queue) {
                /**  @var $queue ListenerQueue */
                $queue->remove($listener);
            }
        }

        return true;
    }

    /**
     * Clear all listeners for a given event
     * @param  string|EventInterface $event
     * @return void
     */
    public function clearListeners($event)
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // 存在对这个事件的监听队列
            if (isset($this->listeners[$event])) {
                unset($this->listeners[$event]);
            }
        } else {
            $this->listeners = [];
        }
    }

    /**
     * @return array
     */
    public function getListenedEvents(): array
    {
        return \array_keys($this->listeners);
    }

    /*******************************************************************************
     * Event manager
     ******************************************************************************/

    /**
     * 添加一个不存在的事件
     * @param EventInterface|string $event | event name
     * @param array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addEvent($event, array $params = []): self
    {
        $event = $this->wrapperEvent($event, null, $params);

        /** @var $event Event */
        if (($event instanceof EventInterface) && !isset($this->events[$event->getName()])) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * 设定一个事件处理
     * @param string|EventInterface $event
     * @param array $params
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setEvent($event, array $params = []): self
    {
        $event = $this->wrapperEvent($event, null, $params);

        if ($event instanceof EventInterface) {
            $this->events[$event->getName()] = $event;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getEvent($name, $default = null)
    {
        return $this->events[$name] ?? $default;
    }

    /**
     * @param $event
     * @return $this
     */
    public function removeEvent($event): self
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        if (isset($this->events[$event])) {
            unset($this->events[$event]);
        }

        return $this;
    }

    /**
     * @param $event
     * @return bool
     */
    public function hasEvent($event): bool
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->events[$event]);
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array $events
     * @throws \InvalidArgumentException
     */
    public function setEvents(array $events)
    {
        foreach ($events as $key => $event) {
            $this->setEvent($event);
        }
    }

    /**
     * @param $event
     * @param null|string $target
     * @param array $params
     * @return EventInterface
     */
    public function wrapperEvent($event, $target = null, array $params = []): EventInterface
    {
        if (!$event instanceof EventInterface) {
            $name = (string)$event;
            $event = clone $this->basicEvent;
            $event->setName($name);
        }

        if ($target) {
            $event->setTarget($target);
        }

        if ($params) {
            $event->setParams($params);
        }

        return $event;
    }

    /**
     * @return int
     */
    public function countEvents(): int
    {
        return \count($this->events);
    }

    /**
     * @return EventManagerInterface
     */
    public function getParent(): EventManagerInterface
    {
        return $this->parent;
    }

    /**
     * @param EventManagerInterface $parent
     */
    public function setParent(EventManagerInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return EventInterface
     */
    public function getBasicEvent(): EventInterface
    {
        return $this->basicEvent;
    }

    /**
     * @param EventInterface $basicEvent
     */
    public function setBasicEvent(EventInterface $basicEvent)
    {
        $this->basicEvent = $basicEvent;
    }
}
