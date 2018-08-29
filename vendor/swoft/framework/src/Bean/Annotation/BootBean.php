<?php

namespace Swoft\Bean\Annotation;

/**
 * The annotation of boot bean
 *
 * @Annotation
 * @Target("CLASS")
 */
class BootBean
{
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
            $this->server = $values['value'];
        }
        if (isset($values['server'])) {
            $this->server = $values['server'];
        }
    }

    /**
     * @return bool
     */
    public function isServer(): bool
    {
        return $this->server;
    }
}