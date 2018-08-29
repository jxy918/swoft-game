<?php

namespace Swoft\Event;

/**
 * Class Event
 * @package Swoft\Event
 * @author    inhere <in.798@qq.com>
 */
class Event implements EventInterface, \ArrayAccess, \Serializable
{
    /** @var string Event name */
    protected $name;

    /** @var array Event params */
    protected $params = [];

    /**
     * @var null|string|mixed
     */
    protected $target;

    /**
     * 停止事件关联的监听器队列的执行
     * @var boolean
     */
    protected $stopPropagation = false;

    /**
     * @param string|null $name
     * @param array $params
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, array $params = [])
    {
        if ($name) {
            $this->setName($name);
        }

        if ($params) {
            $this->params = $params;
        }
    }

    /**
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function checkName(string $name): string
    {
        $name = trim($name, '. ');

        if (!$name || \strlen($name) > 64) {
            throw new \InvalidArgumentException('Set up the name can be a not empty string of not more than 64 characters!');
        }

        return $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setName($name)
    {
        $this->name = self::checkName($name);
    }

    /**
     * set all params
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addParams(array $params): self
    {
        $this->params = \array_merge($this->params, $params);

        return $this;
    }

    /**
     * get all param
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * clear all param
     */
    public function clearParams()
    {
        $old = $this->params;
        $this->params = [];

        return $old;
    }

    /**
     * add a argument
     * @param $name
     * @param $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addParam($name, $value): self
    {
        if (!isset($this->params[$name])) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * set a argument
     * @param $name
     * @param $value
     * @throws  \InvalidArgumentException  If the argument name is null.
     * @return $this
     */
    public function setParam($name, $value): self
    {
        if (null === $name) {
            throw new \InvalidArgumentException('The argument name cannot be null.');
        }

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getParam($name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name): bool
    {
        return isset($this->params[$name]);
    }

    /**
     * @param $name
     */
    public function removeParam($name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }


    /**
     * Get target/context from which event was triggered
     * @return null|string|mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the event target
     * @param  null|string|mixed $target
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Indicate whether or not to stop propagating this event
     * @param  bool $flag
     */
    public function stopPropagation($flag)
    {
        $this->stopPropagation = (bool)$flag;
    }

    /**
     * Has this event indicated event propagation should stop?
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return \serialize([$this->name, $this->params, $this->stopPropagation]);
    }

    /**
     * Unserialize the event.
     * @param   string $serialized The serialized event.
     * @return  void
     */
    public function unserialize($serialized)
    {
        list(
            $this->name,
            $this->params,
            $this->stopPropagation
            ) = \unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * Tell if the given event argument exists.
     * @param   string $name The argument name.
     * @return  boolean  True if it exists, false otherwise.
     */
    public function offsetExists($name): bool
    {
        return $this->hasParam($name);
    }

    /**
     * Get an event argument value.
     * @param   string $name The argument name.
     * @return  mixed  The argument value or null if not existing.
     */
    public function offsetGet($name)
    {
        return $this->getParam($name);
    }

    /**
     * Set the value of an event argument.
     * @param   string $name The argument name.
     * @param   mixed $value The argument value.
     * @return  void
     * @throws \InvalidArgumentException
     */
    public function offsetSet($name, $value)
    {
        $this->setParam($name, $value);
    }

    /**
     * Remove an event argument.
     * @param   string $name The argument name.
     * @return  void
     */
    public function offsetUnset($name)
    {
        $this->removeParam($name);
    }
}
