<?php declare(strict_types=1);

namespace Swoft\Concern;

use function array_merge;

/**
 * Trait DataPropertyTrait
 *
 * @since 2.0
 */
trait DataPropertyTrait
{
    /**
     * User custom data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Set value to data
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get value from data by key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Unset key
     *
     * @param string $key
     */
    public function unset(string $key): void
    {
        unset($this->data[$key]);
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Set multi value to context
     *
     * @param array $map
     * [key => value]
     */
    public function setMulti(array $map): void
    {
        $this->data = array_merge($this->data, $map);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
