<?php declare(strict_types=1);


namespace Swoft\Db;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractPool;
use Swoft\Connection\Pool\Contract\ConnectionInterface;
use Swoft\Connection\Pool\Exception\ConnectionPoolException;
use Swoft\Db\Connection\Connection;
use Swoft\Db\Exception\DbException;

/**
 * Class Pool
 *
 * @since 2.0
 */
class Pool extends AbstractPool
{
    /**
     * Default pool name
     */
    const DEFAULT_POOL = 'db.pool';

    /**
     * Database
     *
     * @var Database
     */
    protected $database;

    /**
     * @return ConnectionInterface
     * @throws ConnectionPoolException
     */
    public function getConnection(): ConnectionInterface
    {
        $connection = parent::getConnection();
        $dbSelector = $this->database->getDbSelector();

        /* @var Connection $connection select db */
        if (!empty($dbSelector)) {
            $dbSelector->select($connection);
        }

        return $connection;
    }

    /**
     * Create connection
     *
     * @return ConnectionInterface
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function createConnection(): ConnectionInterface
    {
        return $this->database->createConnection($this);
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
}
