<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Helper;

use Swoft\App;
use Swoft\Db\Bean\Collector\StatementCollector;
use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Pool;
use Swoft\Pool\PoolInterface;
use Swoft\Db\Pool\Config\DbPoolProperties;

/**
 * DbHelper
 */
class DbHelper
{
    /**
     * Delimiter
     */
    const GROUP_NODE_DELIMITER = '.';

    /**
     * @return string
     */
    public static function getContextSqlKey(): string
    {
        return 'swoft-sql';
    }

    /**
     * @param string $group
     * @param string $node
     *
     * @return \Swoft\Pool\PoolInterface
     */
    public static function getPool(string $group, string $node): PoolInterface
    {
        $poolName        = self::getPoolName($group, $node);
        $notConfig       = $node == Pool::SLAVE && !App::hasPool($poolName);
        $incorrectConfig = App::hasPool($poolName) && empty(App::getPool($poolName)->getPoolConfig()->getUri());

        if ($notConfig || $incorrectConfig) {
            $poolName = self::getPoolName($group, Pool::MASTER);
        }

        return App::getPool($poolName);
    }

    public static function getStatementClassNameByInstance(string $instance): string
    {
        $pool = self::getPool($instance, Pool::MASTER);
        /* @var \Swoft\Db\Pool\Config\DbPoolProperties $poolConfig */
        $poolConfig = $pool->getPoolConfig();
        $driver     = $poolConfig->getDriver();

        $collector = StatementCollector::getCollector();
        if (!isset($collector[$driver])) {
            throw new MysqlException(sprintf('The Statement of %s is not exist!', $driver));
        }
        return $collector[$driver];
    }

    /**
     * @return string
     */
    public static function getContextTransactionsKey(): string
    {
        return sprintf('transactions');
    }

    /**
     * @param string $instance
     *
     * @return string
     */
    public static function getTsInstanceKey(string $instance): string
    {
        return $instance;
    }

    public static function getDriverByInstance(string $instance): string
    {
        $pool = self::getPool($instance, Pool::MASTER);
        /* @var DbPoolProperties $poolConfig */
        $poolConfig = $pool->getPoolConfig();

        return $poolConfig->getDriver();
    }

    /**
     * @param string $group
     * @param string $node
     *
     * @return string
     */
    private static function getPoolName(string $group, string $node): string
    {
        $groupNode = explode(self::GROUP_NODE_DELIMITER, $group);
        if (\count($groupNode) == 2) {
            return $group;
        }

        return sprintf('%s.%s', $group, $node);
    }
}
