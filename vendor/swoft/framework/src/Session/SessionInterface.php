<?php

namespace Swoft\Session;

/**
 * Class SessionInterface - use for TCP, WS connection or HTTP-Session session data manage
 * @since 2.0
 */
interface SessionInterface
{
    /**
     * Get value from context
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set value to context
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void;

    /**
     * Set multi value to context
     *
     * @param array $map
     * [key => value]
     */
    public function setMulti(array $map): void;

    /**
     * Clear resource
     */
    public function clear(): void;
}
