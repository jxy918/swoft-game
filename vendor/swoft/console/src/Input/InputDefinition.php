<?php declare(strict_types=1);

namespace Swoft\Console\Input;

/**
 * Class InputDefinition
 * @since 2.0 refer inhere/console and symfony/console
 */
class InputDefinition
{
    /**
     * @return InputDefinition
     */
    public static function create(): self
    {
        return new self();
    }
}
