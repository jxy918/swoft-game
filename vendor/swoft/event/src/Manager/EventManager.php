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

use Closure;
use InvalidArgumentException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Event\{Event, EventHandlerInterface, EventInterface, EventSubscriberInterface};
use Swoft\Event\Listener\{LazyListener, ListenerPriority, ListenerQueue};
use function array_keys;
use function class_exists;
use function count;
use function is_callable;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function method_exists;
use function strpos;
use function strrpos;
use function substr;
use function trim;

/**
 * Class EventManager
 *
 * @since 2.0
 * @Bean("eventManager", alias="eventDispatcher")
 */
class EventManager implements EventManagerInterface
{
    // Wildcards - all triggered events will flow through
    public const MATCH_ALL = '*';

    /**
     * @var self
     */
    // protected $parent;

    /**
     * @var EventInterface
     */
    protected $basicEvent;

    /**
     * Predefined event store
     *
     * @var EventInterface[]
     * [
     *     'event name' => (object)EventInterface -- event description
     * ]
     */
    protected $events = [];

    /**
     * Listener storage
     *
     * @var ListenerQueue[]
     */
    protected $listeners = [];

    /**
     * All listened event name map
     *
     * @var array [name => 1]
     */
    private $listenedEvents = [];

    /**
     * EventManager constructor.
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->basicEvent = new Event;
    }

    public function __destruct()
    {
        $this->clear();
    }

    /**
     * clear data
     */
    public function clear(): void
    {
        $this->basicEvent = null;
        // clear
        $this->events = $this->listeners = $this->listenedEvents = [];
    }

    /*******************************************************************************
     * Listener manager
     ******************************************************************************/

    /**
     * Attaches a listener to an event
     *
     * @param string                               $event    the event to attach too
     * @param callable|EventHandlerInterface|mixed $callback A callable listener
     * @param int                                  $priority the priority at which the $callback executed
     *
     * @return bool true on success false on failure
     */
    public function attach($event, $callback, $priority = 0): bool
    {
        return $this->addListener($callback, [$event => $priority]);
    }

    /**
     * Detaches a listener from an event
     *
     * @param string         $event    the event to attach too
     * @param callable|mixed $callback a callable function
     *
     * @return bool true on success false on failure
     */
    public function detach($event, $callback): bool
    {
        return $this->removeListener($callback, $event);
    }

    /**
     * @param EventSubscriberInterface $object
     */
    public function addSubscriber(EventSubscriberInterface $object): void
    {
        $priority = ListenerPriority::NORMAL;

        foreach ($object::getSubscribedEvents() as $name => $conf) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = new ListenerQueue;
            }

            $queue = $this->listeners[$name];
            // save event name
            $this->listenedEvents[$name] = 1;

            // only handler method name
            if (is_string($conf)) {
                $queue->add(new LazyListener([$object, $conf]), $priority);
                // with priority ['onPost', ListenerPriority::LOW]
            } elseif (is_string($conf[0])) {
                $queue->add(new LazyListener([$object, $conf[0]]), $conf[1] ?? $priority);
            }
        }
    }

    /**
     * Add a listener and associate it to one (multiple) event
     *
     * @param Closure|callback|mixed $listener   listener
     * @param array|string|int       $definition Event name, priority setting
     *                                           Allowed:
     *                                           $definition = [
     *                                           'event name' => priority(int),
     *                                           'event name1' => priority(int),
     *                                           ]
     *                                           OR
     *                                           $definition = [
     *                                           'event name','event name1',
     *                                           ]
     *                                           OR
     *                                           $definition = 'event name'
     *                                           OR
     *                                           // The priority of the listener
     *                                           $definition = 1
     *
     * @return bool
     */
    public function addListener($listener, $definition = null): bool
    {
        // ensure $listener is a object.
        if (!is_object($listener)) {
            if (is_string($listener) && class_exists($listener)) {
                $listener = new $listener;

                // like 'function' OR '[object, method]'
            } else {
                $listener = new LazyListener($listener);
            }
        }

        $defaultPriority = ListenerPriority::NORMAL;

        if (is_numeric($definition)) { // is priority value
            $defaultPriority = (int)$definition;
            $definition      = null;
        } elseif (is_string($definition)) { // It an event name
            $definition = [$definition => $defaultPriority];
        } elseif ($definition instanceof EventInterface) { // Is an event object, take the name
            $definition = [$definition->getName() => $defaultPriority];
        }

        if ($listener instanceof EventSubscriberInterface) {
            $this->addSubscriber($listener);
            return true;
        }

        if ($definition) {
            foreach ($definition as $name => $priority) {
                if (is_int($name)) {
                    if (!$priority || !is_string($priority)) {
                        continue;
                    }

                    $name     = $priority;
                    $priority = $defaultPriority;
                }

                if (!$name = trim($name, '. ')) {
                    continue;
                }

                if (!isset($this->listeners[$name])) {
                    $this->listeners[$name] = new ListenerQueue;
                }

                $this->listenedEvents[$name] = 1;
                $this->listeners[$name]->add($listener, $priority);
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @throws InvalidArgumentException
     */
    public function triggerEvent(EventInterface $event): EventInterface
    {
        return $this->trigger($event);
    }

    /**
     * @param array $events
     * @param array $args
     *
     * @return array
     * @throws InvalidArgumentException
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
     * Trigger an event. Can accept an EventInterface or will create one if not passed
     *
     * @param string|EventInterface $event  'app.start' 'app.stop'
     * @param mixed|string          $target It is object or string.
     * @param array|mixed           $args
     *
     * @return EventInterface
     * @throws InvalidArgumentException
     */
    public function trigger($event, $target = null, array $args = []): EventInterface
    {
        if ($isString = is_string($event)) {
            $name = trim($event);
        } elseif ($event instanceof EventInterface) {
            $name = trim($event->getName());
        } else {
            throw new InvalidArgumentException('Invalid event params for trigger event handler');
        }

        $shouldCall = [];

        // Have matched listener
        if (isset($this->listenedEvents[$name])) {
            $shouldCall[$name] = '';
        }

        // Like 'app.db.query' => prefix: 'app.db'
        if ($pos = strrpos($name, '.')) {
            $prefix = substr($name, 0, $pos);

            // Have a wildcards listener. eg 'app.db.*'
            $wildcardEvent = $prefix . '.*';
            if (isset($this->listenedEvents[$wildcardEvent])) {
                $shouldCall[$wildcardEvent] = substr($name, $pos + 1);
            }
        }

        // Not found listeners
        if (!$shouldCall) {
            return $isString ? $this->basicEvent : $event;
        }

        /** @var EventInterface $event */
        if ($isString) {
            $event = $this->events[$name] ?? $this->basicEvent;
        }

        // Initial value
        $event->setName($name);
        $event->setParams($args);
        $event->setTarget($target);
        $event->stopPropagation(false);

        // Notify event listeners
        foreach ($shouldCall as $name => $method) {
            $this->triggerListeners($this->listeners[$name], $event, $method);

            if ($event->isPropagationStopped()) {
                return $event;
            }
        }

        // Have global wildcards '*' listener.
        if (isset($this->listenedEvents['*'])) {
            $this->triggerListeners($this->listeners['*'], $event);
        }

        return $event;
    }

    /**
     * Is there a listen queue for the event?
     *
     * @param EventInterface|string $event
     *
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
     * @param EventInterface|string $event
     *
     * @return boolean
     * @see hasListenerQueue() alias method
     */
    public function hasListeners($event): bool
    {
        return $this->hasListenerQueue($event);
    }

    /**
     * Whether there is a listener (for the event)
     *
     * @param mixed                 $listener
     * @param EventInterface|string $event
     *
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
     * Get the priority of a listener for an event
     *
     * @param                        $listener
     * @param string|EventInterface  $event
     *
     * @return int|null
     */
    public function getListenerPriority($listener, $event): ?int
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
     * Get all the listeners for the event
     *
     * @param string|EventInterface $event
     *
     * @return ListenerQueue|null
     */
    public function getListenerQueue($event): ?ListenerQueue
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return $this->listeners[$event] ?? null;
    }

    /**
     * Get all the listeners
     *
     * @return ListenerQueue[]
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    /**
     * Get all the listeners for the event
     *
     * @param string|EventInterface $event
     *
     * @return array
     */
    public function getEventListeners($event): array
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
     * Count the number of listeners that get the event
     *
     * @param string|EventInterface $event
     *
     * @return int
     */
    public function countListeners($event): int
    {
        if ($event instanceof EventInterface) {
            $event = $event->getName();
        }

        return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
    }

    /**
     * Remove listeners for an event
     *
     * @param                            $listener
     * @param null|string|EventInterface $event
     * Remove all listeners named $listener in the listener queue when empty
     * Otherwise, remove the listener for event $event
     *
     * @return bool
     */
    public function removeListener($listener, $event = null): bool
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // There is a listen queue for this event
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
     *
     * @param string|EventInterface $event
     *
     * @return void
     */
    public function clearListeners($event): void
    {
        if ($event) {
            if ($event instanceof EventInterface) {
                $event = $event->getName();
            }

            // There is a listen queue for this event
            if (isset($this->listeners[$event])) {
                unset($this->listeners[$event], $this->listenedEvents[$event]);
            }
        } else {
            $this->listeners = [];
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isListenedEvent(string $name): bool
    {
        return isset($this->listenedEvents[$name]);
    }

    /**
     * @param bool $onlyName
     *
     * @return array
     */
    public function getListenedEvents(bool $onlyName = true): array
    {
        return $onlyName ? array_keys($this->listenedEvents) : $this->listenedEvents;
    }

    /*******************************************************************************
     * Event manager
     ******************************************************************************/

    /**
     * Add a non-existing event
     *
     * @param EventInterface|string $event event name
     * @param array                 $params
     *
     * @return $this
     * @throws InvalidArgumentException
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
     * Set an event object
     *
     * @param string|EventInterface $event
     * @param array                 $params
     *
     * @return $this
     * @throws InvalidArgumentException
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
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getEvent(string $name, $default = null)
    {
        return $this->events[$name] ?? $default;
    }

    /**
     * @param string|EventInterface $event
     *
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
     * @param string|EventInterface $event
     *
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
     *
     * @throws InvalidArgumentException
     */
    public function setEvents(array $events): void
    {
        foreach ($events as $key => $event) {
            $this->setEvent($event);
        }
    }

    /**
     * @param string|EventInterface $event
     * @param null|string           $target
     * @param array                 $params
     *
     * @return EventInterface
     */
    public function wrapperEvent($event, $target = null, array $params = []): EventInterface
    {
        if (!$event instanceof EventInterface) {
            $name  = (string)$event;
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
        return count($this->events);
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
    public function setBasicEvent(EventInterface $basicEvent): void
    {
        $this->basicEvent = $basicEvent;
    }

    /**
     * @param array|ListenerQueue $listeners
     * @param EventInterface      $event
     * @param string              $method
     */
    protected function triggerListeners($listeners, EventInterface $event, string $method = ''): void
    {
        // $handled = false;
        $name     = $event->getName();
        $callable = false === strpos($name, '.');

        // 循环调用监听器，处理事件
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            if (is_object($listener)) {
                if ($listener instanceof EventHandlerInterface) {
                    $listener->handle($event);
                } elseif ($method && method_exists($listener, $method)) {
                    $listener->$method($event);
                } elseif ($callable && method_exists($listener, $name)) {
                    $listener->$name($event);
                } elseif (method_exists($listener, '__invoke')) {
                    $listener($event);
                }
            } elseif (is_callable($listener)) {
                $listener($event);
            }
        }
    }
}
