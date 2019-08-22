<?php declare(strict_types=1);


namespace SwoftTest\Config\Testing;


use Swoft\Annotation\Contract\LoaderInterface;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader implements LoaderInterface
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