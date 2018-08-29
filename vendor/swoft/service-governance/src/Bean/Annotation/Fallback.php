<?php

namespace Swoft\Sg\Bean\Annotation;

/**
 * Definition fallback annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class Fallback
{
    /**
     * @var string
     */
    private $name = "";

    /**
     * Fallback constructor.
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
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}