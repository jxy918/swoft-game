<?php declare(strict_types=1);


namespace Swoft\Redis\Connection;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Co;
use Swoft\Concern\ArrayPropertyTrait;
use Swoft\Connection\Pool\Contract\ConnectionInterface;

/**
 * Class ConnectionManager
 *
 * @since 2.0
 *
 * @Bean()
 */
class ConnectionManager
{
    /**
     * @example
     * [
     *     'tid' => [
     *         'cid' => [
     *             'connectionId' => Connection
     *         ]
     *     ]
     * ]
     */
    use ArrayPropertyTrait;

    /**
     * @param ConnectionInterface $connection
     */
    public function setConnection(ConnectionInterface $connection): void
    {
        $key = sprintf('%d.%d.%d', Co::tid(), Co::id(), $connection->getId());
        $this->set($key, $connection);
    }

    /**
     * @param int $id
     */
    public function releaseConnection(int $id): void
    {
        $key = sprintf('%d.%d.%d', Co::tid(), Co::id(), $id);

        $this->unset($key);
    }

    /**
     * @param bool $final
     */
    public function release(bool $final = false): void
    {
        $key = sprintf('%d.%d', Co::tid(), Co::id());

        $connections = $this->get($key, []);
        foreach ($connections as $connection) {
            if ($connection instanceof ConnectionInterface) {
                $connection->release();
            }
        }
        
        if ($final) {
            $finalKey = sprintf('%d', Co::tid());
            $this->unset($finalKey);
        }
    }

}