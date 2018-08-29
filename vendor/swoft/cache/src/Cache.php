<?php

namespace Swoft\Cache;

use Swoft\App;
use Psr\SimpleCache\CacheInterface;

/**
 * @method string|bool get($key, $default = null)
 * @method bool delete($key)
 * @method bool clear()
 * @method array getMultiple($keys, $default = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method bool deleteMultiple($keys)
 * @method int has($key)
 */
class Cache
{
    /**
     * @var string
     */
    private $driver = 'redis';

    /**
     * @var array
     */
    private $drivers = [];

    /**
     * TODO add serializer mechanism
     * @var null|string
     */
    private $serializer = null;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param  string                $key   The key of the item to store.
     * @param int|double|string|bool $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and the driver
     *                                      supports TTL then the library may set a default value for it or let the
     *                                      driver take care of that.
     * @return bool True on success and false on failure.
     * @throws \InvalidArgumentException If the $value string is not a legal value
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        $valueType = \gettype($value);
        if (! \in_array($valueType, ['integer', 'double', 'boolean', 'string'], true)) {
            // TODO add serializer mechanism handle the other type
            throw new \InvalidArgumentException('Invalid value type');
        }
        return $this->getDriver()->set($key, $value, $ttl);
    }

    /**
     * @param string $method
     * @param array  $arguments
     * @return mixed
     * @throws \RuntimeException If the $method does not exist
     * @throws \InvalidArgumentException If the driver dose not exist
     */
    public function __call($method, $arguments)
    {
        $availableMethods = [
            'has',
            'get',
            'set',
            'delete',
            'getMultiple',
            'setMultiple',
            'deleteMultiple',
            'clear',
        ];
        if (! \in_array($method, $availableMethods, true)) {
            throw new \RuntimeException(sprintf('Method not exist, method=%s', $method));
        }
        $driver = $this->getDriver();
        return $driver->$method(...$arguments);
    }

    /**
     * @param string|null $driver
     * @throws \InvalidArgumentException When driver does not exist
     * @return CacheInterface
     */
    public function getDriver(string $driver = null): CacheInterface
    {
        $currentDriver = $driver ?? $this->driver;
        $drivers = $this->getDrivers();
        if (! isset($drivers[$currentDriver])) {
            throw new \InvalidArgumentException(sprintf('Driver %s not exist', $currentDriver));
        }

        //TODO If driver component not loaded, throw an exception.

        $bean = App::getBean($drivers[$currentDriver]);
        return $bean;
    }

    /**
     * @return array
     */
    private function getDrivers(): array
    {
        return array_merge($this->drivers, $this->defaultDrivers());
    }

    /**
     * Defult drivers
     *
     * @return array
     */
    private function defaultDrivers(): array
    {
        return [
            'redis' => \Swoft\Redis\Redis::class,
        ];
    }
}
