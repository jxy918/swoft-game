<?php declare(strict_types=1);


namespace SwoftTest\Annotation\Testing;


use Swoft\Annotation\Contract\LoaderInterface;

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