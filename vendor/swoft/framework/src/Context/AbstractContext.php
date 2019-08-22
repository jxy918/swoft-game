<?php declare(strict_types=1);

namespace Swoft\Context;

use Swoft\Concern\DataPropertyTrait;
use Swoft\Contract\ContextInterface;

/**
 * Class AbstractContext
 *
 * @since 2.0
 */
abstract class AbstractContext implements ContextInterface
{
    use DataPropertyTrait;

    /**
     * Clear context data
     */
    public function clear(): void
    {
        $this->data = [];
    }
}
