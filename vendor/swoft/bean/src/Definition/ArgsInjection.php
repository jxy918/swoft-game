<?php declare(strict_types=1);

namespace Swoft\Bean\Definition;

/**
 * Class ArgsInjection
 *
 * @since 2.0
 */
class ArgsInjection
{
    /**
     * Arg value
     *
     * @var mixed
     */
    private $value;

    /**
     * Is reference
     *
     * @var bool
     */
    private $isRef = false;

    /**
     * ArgsInjection constructor.
     *
     * @param mixed $value
     * @param bool  $isRef
     */
    public function __construct($value, bool $isRef)
    {
        $this->isRef = $isRef;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isRef(): bool
    {
        return $this->isRef;
    }
}