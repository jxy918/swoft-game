<?php

namespace Swoft\Console\Bean\Annotation;

/**
 * The annotation of command
 *
 * @Annotation
 * @Target("CLASS")
 */
class Command
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @var bool
     */
    private $coroutine = true;

    /**
     * @var bool
     */
    private $server = false;

    /**
     * Command constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        if (isset($values['name'])) {
            $this->name = $values['name'];
        }

        if (isset($values['coroutine'])) {
            $this->coroutine = $values['coroutine'];
        }

        if (isset($values['enabled'])) {
            $this->enabled = (bool)$values['enabled'];
        }

        if (isset($values['server'])) {
            $this->server = $values['server'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCoroutine(): bool
    {
        return $this->coroutine;
    }

    /**
     * @return bool
     */
    public function isServer(): bool
    {
        return $this->server;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
