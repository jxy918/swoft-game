<?php

namespace Swoft\Redis\Operator;

/**
 * Defines an abstraction representing a Redis command.
 */
interface CommandInterface
{
    /**
     * Returns the ID of the Redis command. By convention, command identifiers
     * must always be uppercase.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the arguments for the command.
     *
     * @param array $arguments List of arguments.
     */
    public function setArguments(array $arguments);

    /**
     * Sets the raw arguments for the command without processing them.
     *
     * @param array $arguments List of arguments.
     */
    public function setRawArguments(array $arguments);

    /**
     * Gets the arguments of the command.
     *
     * @return array
     */
    public function getArguments();

    /**
     * Gets the argument of the command at the specified index.
     *
     * @param int $index Index of the desired argument.
     *
     * @return mixed|null
     */
    public function getArgument($index);

    /**
     * Parses a raw response and returns a PHP object.
     *
     * @param string $data Binary string containing the whole response.
     *
     * @return mixed
     */
    public function parseResponse($data);
}
