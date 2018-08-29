<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Redis;

use Swoft\App;
use Swoft\Redis\Exception\RedisException;
use Swoft\Redis\Profile\RedisCommandProvider;
use Swoole\Coroutine\Redis as CoRedis;
use Swoft\Redis\Pool\Config\RedisPoolConfig;

/**
 * Redis connection
 *
 * @method bool select($dbindex)
 */
class RedisConnection extends AbstractRedisConnection
{

    /**
     * Create connection
     *
     * @throws RedisException
     */
    public function createConnection()
    {
        $redis            = $this->initRedis();
        $this->connection = $redis;
    }

    /**
     * @return mixed
     */
    public function receive()
    {
        $result = $this->connection->recv();
        $this->connection->setDefer(false);
        $this->recv = true;

        return $result;
    }

    /**
     * 设置延迟收包
     *
     * @param bool $defer
     */
    public function setDefer($defer = true)
    {
        $this->recv = false;
        $this->connection->setDefer($defer);
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     * @throws RedisException
     */
    public function __call($method, $arguments)
    {
        /* @var RedisCommandProvider $commandProvider */
        $commandProvider = App::getBean(RedisCommandProvider::class);
        $command         = $commandProvider->createCommand($method, $arguments);
        $arguments       = $command->getArguments();
        $method          = $command->getId();

        return parent::__call($method, $arguments);
    }


    /**
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     *
     * @return CoRedis
     * @throws RedisException
     */
    protected function getConnectRedis(string $host, int $port, int $timeout): CoRedis
    {
        /* @var RedisPoolConfig $poolConfig */
        $poolConfig = $this->pool->getPoolConfig();
        $serialize  = $poolConfig->getSerialize();
        $serialize  = ((int)$serialize == 0) ? false : true;
        $redis      = new CoRedis();
        $result     = $redis->connect($host, $port, $serialize);
        if ($result == false) {
            $error = sprintf('Redis connection failure host=%s port=%d', $host, $port);
            throw new RedisException($error);
        }

        return $redis;
    }
}
