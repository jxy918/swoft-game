<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Core\ResultInterface;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Helper\DbHelper;
use Swoft\Db\Helper\EntityHelper;
use Swoft\Helper\PoolHelper;
use Swoft\Log\Log;
use Swoft\Pool\ConnectionInterface;
use Swoft\Db\Pool\Config\DbPoolProperties;
use Swoft\Db\Pool\DbPool;

/**
 * Db
 */
class Db
{
    /**
     * Return one
     */
    const RETURN_ONE = 1;

    /**
     * Return rows
     */
    const RETURN_ROWS = 2;

    /**
     * Return fetch
     */
    const RETURN_FETCH = 3;

    /**
     * Return insertid
     */
    const RETURN_INSERTID = 4;

    /**
     * Query by sql
     *
     * @param string $sql
     * @param array $params
     * @param string $instance
     * @param string $className
     * @param array $resultDecorators
     * @return \Swoft\Core\ResultInterface|DbResult
     * @throws DbException
     */
    public static function query(string $sql, array $params = [], string $instance = Pool::INSTANCE, string $className = '', array $resultDecorators = []): ResultInterface
    {
        $type     = self::getOperation($sql);
        $instance = explode('.', $instance);
        list($instance, $node, $db) = array_pad($instance, 3, '');

        list($instance, $node) = self::getInstanceAndNodeByType($instance, $node, $type);
        /* @var AbstractDbConnection $connection */
        $connection = self::getConnection($instance, $node);

        if (!empty($db)) {
            $connection->selectDb($db);
        }

        /* @var DbPool $pool */
        $pool = $connection->getPool();
        /* @var DbPoolProperties $poolConfig */
        $poolConfig = $pool->getPoolConfig();
        $driver = $poolConfig->getDriver();

        if (App::isCoContext()) {
            $connection->setDefer();
        }

        $sqlId = uniqid();
        $profileKey = sprintf('%s.%s', $driver, $sqlId);
        Log::debug(sprintf('Execute sqlId=%s , sql=%s', $sqlId, $sql));

        Log::profileStart($profileKey);
        $connection->prepare($sql);
        $params = self::transferParams($params);
        $result = $connection->execute($params);

        $dbResult = self::getResult($result, $connection, $profileKey);
        $dbResult->setType($type);
        $dbResult->setClassName($className);
        $dbResult->setDecorators($resultDecorators);

        return $dbResult;
    }

    /**
     * @param string $instance
     */
    public static function beginTransaction(string $instance = Pool::INSTANCE)
    {
        /* @var AbstractDbConnection $connection */
        $connection = self::getConnection($instance, Pool::MASTER, 'ts');
        $connection->setAutoRelease(false);
        $connection->beginTransaction();

        self::beginTransactionContext($connection, $instance);
    }

    /**
     * @param string $instance
     *
     * @throws DbException
     */
    public static function rollback(string $instance = Pool::INSTANCE)
    {
        /* @var AbstractDbConnection $connection */
        $connection = self::getTransactionConnection($instance);
        if ($connection === null) {
            throw new DbException('No transaction needs to be rolled back');
        }

        $connection->rollback();
        self::closetTransactionContext($connection, $instance);
    }

    /**
     * @param string $instance
     *
     * @throws DbException
     */
    public static function commit(string $instance = Pool::INSTANCE)
    {
        /* @var AbstractDbConnection $connection */
        $connection = self::getTransactionConnection($instance);
        if ($connection === null) {
            throw new DbException('No transaction needs to be committed');
        }

        $connection->commit();
        self::closetTransactionContext($connection, $instance);
    }

    /**
     * @param string $instance
     * @param string $node
     * @param int    $type
     *
     * @return array
     */
    private static function getInstanceAndNodeByType(string $instance, string $node, int $type): array
    {
        if (!empty($node)) {
            return [$instance, $node];
        }

        if ($type === self::RETURN_ROWS || $type === self::RETURN_INSERTID) {
            return [$instance, Pool::MASTER];
        }

        return [$instance, Pool::SLAVE];
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    private static function getOperation(string $sql): string
    {
        // remove annotion and blank
        $sql = preg_replace('/\/\*[^\/]+\*\//', '', $sql);
        $sql = trim($sql);
        $sql = strtoupper($sql);

        if (strpos($sql, 'INSERT') === 0) {
            return self::RETURN_INSERTID;
        }

        if (strpos($sql, 'UPDATE') === 0 || strpos($sql, 'DELETE') === 0) {
            return self::RETURN_ROWS;
        }

        return self::RETURN_FETCH;
    }

    /**
     * @param string $instance
     * @param string $node
     *
     * @return ConnectionInterface
     */
    private static function getConnection(string $instance, string $node, $ts = 'query'): ConnectionInterface
    {
        $transactionConnection = self::getTransactionConnection($instance);
        if ($transactionConnection !== null) {
            return $transactionConnection;
        }

        $pool = DbHelper::getPool($instance, $node);

        return $pool->getConnection();
    }

    /**
     * @param string $instance
     *
     * @return mixed
     */
    private static function getTransactionConnection(string $instance)
    {
        $contextTsKey  = DbHelper::getContextTransactionsKey();
        $contextCntKey = PoolHelper::getContextCntKey();
        $instanceKey   = DbHelper::getTsInstanceKey($instance);
        /* @var \SplStack $tsStack */
        $tsStack = RequestContext::getContextDataByChildKey($contextTsKey, $instanceKey, new \SplStack());
        if ($tsStack->isEmpty()) {
            return null;
        }
        $cntId      = $tsStack->offsetGet(0);
        $connection = RequestContext::getContextDataByChildKey($contextCntKey, $cntId, null);

        return $connection;
    }

    /**
     * @param ConnectionInterface $connection
     * @param string              $instance
     */
    private static function beginTransactionContext(ConnectionInterface $connection, string $instance = Pool::INSTANCE)
    {
        $cntId        = $connection->getConnectionId();
        $contextTsKey = DbHelper::getContextTransactionsKey();
        $instanceKey  = DbHelper::getTsInstanceKey($instance);

        /* @var \SplStack $tsStack */
        $tsStack = RequestContext::getContextDataByChildKey($contextTsKey, $instanceKey, new \SplStack());
        $tsStack->push($cntId);
        RequestContext::setContextDataByChildKey($contextTsKey, $instanceKey, $tsStack);
    }

    /**
     * @param AbstractDbConnection $connection
     * @param string               $instance
     */
    private static function closetTransactionContext(AbstractDbConnection $connection, string $instance = Pool::INSTANCE)
    {
        $contextTsKey = DbHelper::getContextTransactionsKey();
        $instanceKey  = DbHelper::getTsInstanceKey($instance);

        /* @var \SplStack $tsStack */
        $tsStack = RequestContext::getContextDataByChildKey($contextTsKey, $instanceKey, new \SplStack());
        if (!$tsStack->isEmpty()) {
            $tsStack->pop();
        }
        RequestContext::setContextDataByChildKey($contextTsKey, $instanceKey, $tsStack);
        $connection->release(true);
    }

    /**
     * @param mixed               $result
     * @param ConnectionInterface $connection
     * @param string              $profileKey
     *
     * @return \Swoft\Db\DbResult
     */
    private static function getResult($result, ConnectionInterface $connection = null, string $profileKey = '')
    {
        if (App::isCoContext()) {
            return new DbCoResult($result, $connection, $profileKey);
        }

        return new DbDataResult($result, $connection, $profileKey);
    }

    /**
     * @param $params
     *
     * @throws DbException
     * @return array
     */
    private static function transferParams($params): array
    {
        $newParams = [];
        foreach ($params as $key => $value) {
            list($nkey, $nvalue) = EntityHelper::transferParameter($key, $value, null);
            $newParams[$nkey] = $nvalue;
        }

        return $newParams;
    }
}
