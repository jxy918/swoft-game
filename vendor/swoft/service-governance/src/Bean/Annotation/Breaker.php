<?php

namespace Swoft\Sg\Bean\Annotation;

/**
 * the annotation of breaker
 *
 * @Annotation
 * @Target("CLASS")
 */
class Breaker
{
    /**
     * the name of breaker
     *
     * @var string
     */
    private $name = "";

    /**
     * Breaker constructor.
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
