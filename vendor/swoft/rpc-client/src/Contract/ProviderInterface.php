<?php declare(strict_types=1);


namespace Swoft\Rpc\Client\Contract;

use Swoft\Rpc\Client\Client;

/**
 * Class ProviderInterface
 *
 * @since 2.0
 */
interface ProviderInterface
{
    /**
     * @param Client $client
     *
     * @return array
     *
     * @example
     * [
     *     'host:port',
     *     'host:port',
     *     'host:port',
     * ]
     */
    public function getList(Client $client): array;
}