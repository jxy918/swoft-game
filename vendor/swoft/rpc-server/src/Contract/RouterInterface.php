<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Contract;

/**
 * Class RouterInterface
 *
 * @since 2.0
 */
interface RouterInterface extends \Swoft\Contract\RouterInterface
{
    /**
     * @param string $interface
     * @param string $version
     * @param string $className
     */
    public function addRoute(string $interface, string $version, string $className): void;

    /**
     * @param string $version
     * @param string $interface
     *
     * @return array
     */
    public function match(string $version, string $interface): array;
}