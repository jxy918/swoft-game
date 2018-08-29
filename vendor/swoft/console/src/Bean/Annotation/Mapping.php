<?php

namespace Swoft\Console\Bean\Annotation;

/**
 * The annotation of mapping
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Mapping
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * Mapping constructor.
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