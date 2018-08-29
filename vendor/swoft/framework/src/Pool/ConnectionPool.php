<?php

namespace Swoft\Pool;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Exception\ConnectionException;
use Swoft\Exception\PoolException;
use Swoft\Helper\PoolHelper;
use Swoole\Coroutine\Channel;

/**
 * Class ConnectPool
 */
abstract class ConnectionPool implements PoolInterface
{
    /**
     * Current connection count
     *
     * @var int
     */
    protected $currentCount = 0;

    /**
     * Pool config
     *
     * @var PoolConfigInterface
     */
    protected $poolConfig;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var \SplQueue
     */
    protected $queue;

    /**
     * Initialization
     */
    public function init()
    {
        if (empty($this->poolConfig)) {
            throw new PoolException('You must to set poolConfig by @Inject!');
        }

        if (App::isWorkerStatus()) {
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        } else {
            $this->queue = new \SplQueue();
        }
    }

    /**
     * Get connection
     *
     * @throws ConnectionException;
     * @return ConnectionInterface
     */
    public function getConnection():ConnectionInterface
    {
        if (App::isCoContext()) {
            $connection = $this->getConnectionByChannel();
        } else {
            $connection = $this->getConnectionByQueue();
        }

        if ($connection->check() == false) {
            $connection->reconnect();
        }

        $this->addContextConnection($connection);
        return $connection;
    }

    /**
     * Release connection
     *
     * @param ConnectionInterface $connection
     */
    public function release(ConnectionInterface $connection)
    {
        $connectionId = $connection->getConnectionId();
        $connection->updateLastTime();
        $connection->setRecv(true);
        $connection->setAutoRelease(true);

        if (App::isCoContext()) {
            $this->releaseToChannel($connection);
        } else {
            $this->releaseToQueue($connection);
        }

        $this->removeContextConnection($connectionId);
    }

    /**
     * Get one address
     *
     * @return string "127.0.0.1:88"
     */
    public function getConnectionAddress():string
    {
        $serviceList  = $this->getServiceList();
        if (App::hasBean('balancerSelector')) {
            $balancerType = $this->poolConfig->getBalancer();
            $balancer     = balancer()->select($balancerType);
            return $balancer->select($serviceList);
        }
        return current($serviceList);
    }

    /**
     * Get service list
     *
     * @return array
     * <pre>
     * [
     *   "127.0.0.1:88",
     *   "127.0.0.1:88"
     * ]
     * </pre>
     */
    protected function getServiceList()
    {
        $name = $this->poolConfig->getName();
        if ($this->poolConfig->isUseProvider() && App::hasBean('providerSelector')) {
            $type = $this->poolConfig->getProvider();

            return provider()->select($type)->getServiceList($name);
        }

        $uri = $this->poolConfig->getUri();
        if (empty($uri)) {
            $error = sprintf('Service does not configure uri name=%s', $name);
            App::error($error);
            throw new \InvalidArgumentException($error);
        }

        return $uri;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->poolConfig->getTimeout();
    }

    /**
     * @return PoolConfigInterface
     */
    public function getPoolConfig(): PoolConfigInterface
    {
        return $this->poolConfig;
    }

    /**
     * Release to queue
     *
     * @param $connection
     */
    private function releaseToQueue(ConnectionInterface $connection)
    {
        if ($this->queue->count() < $this->poolConfig->getMaxActive()) {
            $this->queue->push($connection);
        }
    }

    /**
     * Release to channel
     *
     * @param $connection
     */
    private function releaseToChannel(ConnectionInterface $connection)
    {
        $stats     = $this->channel->stats();
        $maxActive = $this->poolConfig->getMaxActive();
        if ($stats['queue_num'] < $maxActive) {
            $this->channel->push($connection);
        }
    }

    /**
     * Get connection by queue
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByQueue(): ConnectionInterface
    {
        if($this->queue == null){
            $this->queue = new \SplQueue();
        }
        if (!$this->queue->isEmpty()) {
            return $this->getEffectiveConnection($this->queue->count(), false);
        }

        if ($this->currentCount >= $this->poolConfig->getMaxActive()) {
            throw new ConnectionException('Connection pool queue is full');
        }

        $connect = $this->createConnection();
        $this->currentCount++;

        return $connect;
    }

    /***
     * Get connection by channel
     *
     * @return ConnectionInterface
     * @throws ConnectionException
     */
    private function getConnectionByChannel(): ConnectionInterface
    {
        if($this->channel === null){
            $this->channel = new Channel($this->poolConfig->getMaxActive());
        }

        $stats = $this->channel->stats();
        if ($stats['queue_num'] > 0) {
            return $this->getEffectiveConnection($stats['queue_num']);
        }

        $maxActive = $this->poolConfig->getMaxActive();
        if ($this->currentCount < $maxActive) {
            $connection = $this->createConnection();
            $this->currentCount++;

            return $connection;
        }

        $maxWait = $this->poolConfig->getMaxWait();
        if ($maxWait != 0 && $stats['consumer_num'] >= $maxWait) {
            throw new ConnectionException(sprintf('Connection pool waiting queue is full, maxActive=%d,maxWait=%d,currentCount=%d', $maxActive, $maxWait, $this->currentCount));
        }

        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        if ($maxWaitTime == 0) {
            return $this->channel->pop();
        }

        $writes = [];
        $reads       = [$this->channel];
        $result      = $this->channel->select($reads, $writes, $maxWaitTime);

        if ($result === false || empty($reads)) {
            throw new ConnectionException('Connection pool waiting queue timeout, timeout='.$maxWaitTime);
        }

        $readChannel = $reads[0];

        return $readChannel->pop();
    }

    /**
     * Get effective connection
     *
     * @param int  $queueNum
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getEffectiveConnection(int $queueNum, bool $isChannel = true): ConnectionInterface
    {
        $minActive = $this->poolConfig->getMinActive();
        if ($queueNum <= $minActive) {
            return $this->getOriginalConnection($isChannel);
        }

        $time        = time();
        $moreActive  = $queueNum - $minActive;
        $maxWaitTime = $this->poolConfig->getMaxWaitTime();
        for ($i = 0; $i < $moreActive; $i++) {
            /* @var ConnectionInterface $connection */
            $connection = $this->getOriginalConnection($isChannel);;
            $lastTime = $connection->getLastTime();
            if ($time - $lastTime < $maxWaitTime) {
                return $connection;
            }
            $this->currentCount--;
        }

        return $this->getOriginalConnection($isChannel);
    }

    /**
     * Get original connection
     *
     * @param bool $isChannel
     *
     * @return ConnectionInterface
     */
    private function getOriginalConnection(bool $isChannel = true): ConnectionInterface
    {
        if ($isChannel) {
            return $this->channel->pop();
        }

        return $this->queue->shift();
    }

    /**
     * @param \Swoft\Pool\ConnectionInterface $connection
     */
    private function addContextConnection(ConnectionInterface $connection)
    {
        $connectionId  = $connection->getConnectionId();
        $connectionKey = PoolHelper::getContextCntKey();
        RequestContext::setContextDataByChildKey($connectionKey, $connectionId, $connection);
    }

    /**
     * @param string $connectionId
     */
    private function removeContextConnection(string $connectionId)
    {
        $connectionKey = PoolHelper::getContextCntKey();
        RequestContext::removeContextDataByChildKey($connectionKey, $connectionId);
    }
}
