<?php declare(strict_types=1);


namespace Swoft\Annotation\Contract;

/**
 * Class LoaderInterface
 *
 * @since 2.0
 */
interface LoaderInterface
{
    /**
     * Get namespace and dir
     *
     * @return array
     * [
     *     namespace => dir path
     * ]
     */
    public function getPrefixDirs(): array;
}
