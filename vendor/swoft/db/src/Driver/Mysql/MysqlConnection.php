<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Db\Driver\Mysql;

use Swoft\App;
use Swoft\Db\AbstractDbConnection;
use Swoft\Db\Bean\Annotation\Connection;
use Swoft\Db\Exception\MysqlException;
use Swoole\Coroutine\Mysql;

/**
 * Mysql connection
 *
 * @Connection()
 */
class MysqlConnection extends AbstractDbConnection
{
    /**
     * @var Mysql
     */
    private $connection;

    /**
     * @var string
     */
    private $sql = '';

    /**
     * @var mixed
     */
    private $result;

    /**
     * Prepare
     *
     * @param string $sql
     */
    public function prepare(string $sql)
    {
        $this->sql = $sql;
    }

    /**
     * Create connection
     *
     * @throws \InvalidArgumentException
     */
    public function createConnection()
    {
        $uri                = $this->pool->getConnectionAddress();
        $options            = $this->parseUri($uri);
        $options['timeout'] = $this->pool->getTimeout();

        // init
        $mysql = new MySQL();
        $mysql->connect([
            'host'     => $options['host'],
            'port'     => $options['port'],
            'user'     => $options['user'],
            'password' => $options['password'],
            'database' => $options['database'],
            'timeout'  => $options['timeout'],
            'charset'  => $options['charset'],
        ]);

        // error
        if ($mysql->connected === false) {
            throw new MysqlException('Database connection error，error=' . $mysql->connect_error);
        }

        $this->originDb   = $options['database'];
        $this->connection = $mysql;
    }

    /**
     * Execute
     *
     * @param array|null $params
     *
     * @return array|bool
     */
    public function execute(array $params = [])
    {
        $this->formatSqlByParams($params);
        $result = $this->connection->query($this->sql);
        if ($result === false) {
            throw new MysqlException('Mysql execute error，connectError=' . $this->connection->connect_error . ' error=' . $this->connection->error);
        }

        $this->pushSqlToStack($this->sql);

        return $result;
    }

    /**
     * @return mixed
     */
    public function receive()
    {
        $result = $this->connection->recv();
        if ($result === false) {
            throw new MysqlException('Mysql recv error，connectError=' . $this->connection->connect_error . ' error=' . $this->connection->error);
        }
        $this->connection->setDefer(false);

        $this->recv   = true;
        $this->result = $result;

        return $result;
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getInsertId()
    {
        return $this->connection->insert_id;
    }

    /**
     * @return int
     */
    public function getAffectedRows(): int
    {
        return $this->connection->affected_rows;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->connection->query('begin;');
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        if (!$this->recv) {
            $this->receive();
            App::error('You forget to getResult() before rollback !');
        }
        $this->connection->query('rollback;');
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        if (!$this->recv) {
            $this->receive();
            App::error('You forget to getResult() before commit !');
        }
        $this->connection->query('commit;');
    }

    /**
     * @param string $db
     */
    public function selectDb(string $db)
    {
        $this->connection->query(sprintf('use %s', $db));
        $this->currentDb = $db;
    }

    /**
     * @param bool $defer
     */
    public function setDefer($defer = true)
    {
        $this->recv = false;
        $result     = $this->connection->setDefer($defer);
    }

    /**
     * @return void
     */
    public function reconnect()
    {
        $this->createConnection();
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->connection->connected;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * Destroy sql
     */
    public function destroy()
    {
        $this->sql = '';
    }

    /**
     * 格式化sql参数
     *
     * @param array|null $params
     */
    private function formatSqlByParams(array $params = null)
    {
        if (empty($params)) {
            return;
        }

        $newParams = [];
        foreach ($params as $key => $value) {
            if ($value === null) {
                $value = " null ";
            } elseif (\is_array($value)) {
                $value = "'" . \implode("','", \array_map('addslashes', $value)) . "'";
            } else {
                $value = "'" . \addslashes($value) . "'";
            }

            if (\is_int($key)) {
                $key = sprintf('?%d', $key);
            }
            $newParams[$key] = $value;
        }

        // ?方式传递参数
        if (strpos($this->sql, '?') !== false) {
            $this->transferQuestionMark();
        }

        $this->sql = strtr($this->sql, $newParams);
    }

    /**
     * 格式化?标记
     */
    private function transferQuestionMark()
    {
        $sqlAry   = explode('?', $this->sql);
        $sql      = '';
        $maxBlock = \count($sqlAry);
        for ($i = 0; $i < $maxBlock; $i++) {
            $n   = $i;
            $sql .= $sqlAry[$i];
            if ($maxBlock > $i + 1) {
                $sql .= '?' . $n . ' ';
            }
        }
        $this->sql = $sql;
    }
}
