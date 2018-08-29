<?php

namespace Swoft\Bean\Annotation;

/**
 * The annotation of definition
 *
 * @Annotation
 * @Target("CLASS")
 */
class Definition
{
    /**
     * @var string
     */
    private $name = "";

    /**
     * Definition constructor.
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