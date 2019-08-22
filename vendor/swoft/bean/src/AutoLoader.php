<?php declare(strict_types=1);

namespace Swoft\Bean;

/**
 * Class AutoLoader
 * @since 2.0
 */
class AutoLoader extends \Swoft\Annotation\AutoLoader
{
    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }
}
