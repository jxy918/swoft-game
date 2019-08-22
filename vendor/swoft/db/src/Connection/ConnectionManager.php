<?php declare(strict_types=1);


namespace Swoft\Db\Connection;

use ReflectionException;
use RuntimeException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Concern\ArrayPropertyTrait;
use Swoft\Connection\Pool\Contract\ConnectionInterface as BaseConnection;

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
     *  'tid' => [
     *      'transaction' => [
     *          'cid' => [
     *              'poolName' => [
     *                  'transactions' => 0,
     *                  'connection' => Connection
     *              ]
     *          ]
     *      ],
     *
     *     'connection' => [
     *          'cid' => [
     *              'poolName' => [
     *                  'connectionId' => Connection
     *              ]
     *          ]
     *      ]
     *   ],
     *  'tid2' => [
     *      'transaction' => [
     *          'cid' => [
     *              'poolName' => [
     *                  'transactions' => 0,
     *                  'connection' => Connection
     *              ]
     *          ]
     *      ],
     *
     *     'connection' => [
     *          'cid' => [
     *              'poolName' => [
     *                  'connectionId' => Connection
     *              ]
     *          ]
     *      ]
     *   ],
     * ]
     */
    use ArrayPropertyTrait;


    /**
     * @param BaseConnection $connection
     * @param string         $poolName
     */
    public function setOrdinaryConnection(BaseConnection $connection, string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.connection.%d.%s.%d', Co::tid(), Co::id(), $poolName, $connection->getId());
        $this->set($key, $connection);
    }

    /**
     * @param int    $id
     * @param string $poolName
     */
    public function releaseOrdinaryConnection(int $id, string $poolName)
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.connection.%d.%s.%d', Co::tid(), Co::id(), $poolName, $id);
        $this->unset($key);
    }

    /**
     * @param string $poolName
     *
     * @return bool
     */
    public function isTransaction(string $poolName): bool
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.connection', Co::tid(), Co::id(), $poolName);
        return $this->has($key);
    }

    /**
     * Inc transactions for transaction
     *
     * @param string $poolName
     */
    public function incTransactionTransactons(string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.transactions', Co::tid(), Co::id(), $poolName);

        $transactions = $this->get($key, 0);
        $this->set($key, $transactions + 1);
    }

    /**
     * Dec transactions for transaction
     *
     * @param string $poolName
     */
    public function decTransactionTransactons(string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.transactions', Co::tid(), Co::id(), $poolName);

        $transactions = $this->get($key, 0);
        if ($transactions <= 0) {
            return;
        }

        $this->set($key, $transactions - 1);
    }

    /**
     * @param int    $transactions
     * @param string $poolName
     */
    public function setTransactionTransactons(int $transactions, string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.transactions', Co::tid(), Co::id(), $poolName);
        $this->set($key, $transactions);
    }

    /**
     * @param string $poolName
     *
     * @return int
     */
    public function getTransactionTransactons(string $poolName): int
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.transactions', Co::tid(), Co::id(), $poolName);
        return $this->get($key, 0);
    }

    /**
     * @param BaseConnection $connection
     * @param string         $poolName
     */
    public function setTransactionConnection(BaseConnection $connection, string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.connection', Co::tid(), Co::id(), $poolName);
        $this->set($key, $connection);
    }

    /**
     * @param string $poolName
     *
     * @return Connection
     */
    public function getTransactionConnection(string $poolName): Connection
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s.connection', Co::tid(), Co::id(), $poolName);
        $con      = $this->get($key, null);
        if (!$con instanceof Connection) {
            throw new RuntimeException('Transaction is not beginning!');
        }

        return $con;
    }

    /**
     * Release transaction
     *
     * @param string $poolName
     */
    public function releaseTransaction(string $poolName): void
    {
        $poolName = $this->formatName($poolName);
        $key      = sprintf('%d.transaction.%d.%s', Co::tid(), Co::id(), $poolName);
        $this->unset($key);
    }

    /**
     * release
     *
     * @param bool $final
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function release(bool $final = false): void
    {
        // Final release
        if ($final) {
            $finalKey = sprintf('%d', Co::tid());
            $this->unset($finalKey);
            return;
        }

        // Release current coroutine
        $ordKey = sprintf('%d.connection.%d', Co::tid(), Co::id());
        $tsKey  = sprintf('%d.transaction.%d', Co::tid(), Co::id());

        $ordConnections = $this->get($ordKey, []);
        foreach ($ordConnections as $poolName => $ordPoolConnection) {
            foreach ($ordPoolConnection as $ordConId => $ordConnection) {
                if (!$ordConnection instanceof Connection) {
                    continue;
                }

                if ($ordConnection->inTransaction()) {
                    $ordConnection->forceRollBack(0);
                    continue;
                }

                $ordConnection->release(true);
            }
        }

        $this->unset($ordKey);
        $this->unset($tsKey);
    }

    /**
     * Format name
     *
     * @param string $name
     *
     * @return string
     */
    private function formatName(string $name): string
    {
        return str_replace('.', '-', $name);
    }
}
