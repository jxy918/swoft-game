<?php

namespace Swoft\Rpc\Server\Bean\Annotation;

/**
 * Service annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class Service
{
    /**
     * @var string
     */
    private $version = "0";

    /**
     * Service constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->version = $values['value'];
        }
        if (isset($values['version'])) {
            $this->version = $values['version'];
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
