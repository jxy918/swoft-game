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

use ArrayAccess;
use InvalidArgumentException;
use Serializable;
use function array_merge;
use function serialize;
use function strlen;
use function trim;
use function unserialize;

/**
 * Class Event
 *
 * @since 2.0
 */
class Event implements EventInterface, ArrayAccess, Serializable
{
    /**
     * @var string Event name
     */
    private $name = '';

    /**
     * @var null|string|mixed
     */
    protected $target;

    /**
     * @var array Event params
     */
    protected $params = [];

    /**
     * Stop execution of the listener queue associated with the event
     *
     * @var boolean
     */
    protected $stopPropagation = false;

    /**
     * @param string $name
     * @param array  $params
     *
     * @return Event
     */
    public static function create(string $name = '', array $params = []): self
    {
        return new static($name, $params);
    }

    /**
     * @param string $name
     * @param array  $params
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $name = '', array $params = [])
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
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public static function checkName(string $name): string
    {
        $name = trim($name, '. ');
        if (!$name || strlen($name) > 128) {
            throw new InvalidArgumentException('Setup the name cannot be a empty string of not more than 128 characters!');
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
     * @throws InvalidArgumentException
     */
    public function setName(string $name): void
    {
        $this->name = self::checkName($name);
    }

    /**
     * Set all params
     *
     * @param array $params
     */
    public function setParams(array $params): void
    {
        if ($params) {
            $this->params = $params;
        }
    }

    /**
     * @param array $params
     *
     * @return void
     */
    public function addParams(array $params): void
    {
        if ($params) {
            $this->params = array_merge($this->params, $params);
        }
    }

    /**
     * get all param
     *
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
        // clear
        $this->params = [];

        return $old;
    }

    /**
     * add a argument
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addParam($name, $value): self
    {
        if (!isset($this->params[$name])) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * Set a argument
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throws  InvalidArgumentException  If the argument name is null.
     */
    public function setParam($name, $value): self
    {
        if (null === $name) {
            throw new InvalidArgumentException('The argument name cannot be null.');
        }

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @param int|string $key
     * @param mixed      $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParam($name): bool
    {
        return isset($this->params[$name]);
    }

    /**
     * @param string $name
     */
    public function removeParam($name)
    {
        if (isset($this->params[$name])) {
            unset($this->params[$name]);
        }
    }

    /**
     * Get target/context from which event was triggered
     *
     * @return null|string|mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set the event target
     *
     * @param null|string|mixed $target
     *
     * @return void
     */
    public function setTarget($target): void
    {
        $this->target = $target;
    }

    /**
     * Indicate whether or not to stop propagating this event
     *
     * @param bool $flag
     */
    public function stopPropagation($flag)
    {
        $this->stopPropagation = (bool)$flag;
    }

    /**
     * Has this event indicated event propagation should stop?
     *
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
        return serialize([$this->name, $this->params, $this->stopPropagation]);
    }

    /**
     * Unserialize the event.
     *
     * @param string $serialized The serialized event.
     *
     * @return  void
     */
    public function unserialize($serialized): void
    {
        [
            $this->name,
            $this->params,
            $this->stopPropagation
        ] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * Tell if the given event argument exists.
     *
     * @param string $name The argument name.
     *
     * @return  boolean  True if it exists, false otherwise.
     */
    public function offsetExists($name): bool
    {
        return $this->hasParam($name);
    }

    /**
     * Get an event argument value.
     *
     * @param string $name The argument name.
     *
     * @return  mixed  The argument value or null if not existing.
     */
    public function offsetGet($name)
    {
        return $this->getParam($name);
    }

    /**
     * Set the value of an event argument.
     *
     * @param string $name  The argument name.
     * @param mixed  $value The argument value.
     *
     * @return  void
     * @throws InvalidArgumentException
     */
    public function offsetSet($name, $value): void
    {
        $this->setParam($name, $value);
    }

    /**
     * Remove an event argument.
     *
     * @param string $name The argument name.
     *
     * @return  void
     */
    public function offsetUnset($name): void
    {
        $this->removeParam($name);
    }
}
