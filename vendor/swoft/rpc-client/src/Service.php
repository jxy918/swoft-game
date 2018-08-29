<?php

namespace Swoft\Rpc\Client;

use Swoft\App;
use Swoft\Core\ResultInterface;
use Swoft\Helper\JsonHelper;
use Swoft\Helper\PhpHelper;
use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\PoolInterface;
use Swoft\Rpc\Client\Exception\RpcClientException;
use Swoft\Rpc\Client\Service\AbstractServiceConnection;
use Swoft\Rpc\Client\Service\ServiceCoResult;
use Swoft\Rpc\Client\Service\ServiceDataResult;
use Swoft\Sg\Circuit\CircuitBreaker;

/**
 * The service trait
 */
class Service
{
    /**
     * The prefix of defer method
     *
     * @var string
     */
    const DEFER_PREFIX = 'defer';

    /**
     * The name of service
     *
     * @var string``
     */
    protected $name;

    /**
     * @var string
     */
    protected $version;

    /**
     * The name of pool
     *
     * @var string
     */
    protected $poolName;

    /**
     * The name of breaker
     *
     * @var string
     */
    protected $breakerName;

    /**
     * The name of packer
     *
     * @var string
     */
    protected $packerName;

    /**
     * @var string
     */
    protected $interface;

    /**
     * @var string
     */
    protected $fallback;

    /**
     * Do call service
     *
     * @param string $func
     * @param array  $params
     *
     * @throws \Throwable
     * @return mixed
     */
    public function call(string $func, array $params)
    {
        $profileKey = $this->interface . '->' . $func;
        $fallback   = $this->getFallbackHandler($func);

        try {
            $connectPool    = $this->getPool();
            $circuitBreaker = $this->getBreaker();

            /* @var $client AbstractServiceConnection */
            $client = $connectPool->getConnection();

            $packer   = service_packer();
            $type     = $this->getPackerName();
            $data     = $packer->formatData($this->interface, $this->version, $func, $params);
            $packData = $packer->pack($data, $type);

            $result = $circuitBreaker->call([$client, 'send'], [$packData], $fallback);
            if ($result === null || $result === false) {
                return null;
            }

            App::profileStart($profileKey);
            $result = $client->receive();
            App::profileEnd($profileKey);
            $client->release(true);

            App::debug(sprintf('%s call %s success, data=%', $this->interface, $func, json_encode($data, JSON_UNESCAPED_UNICODE)));
            $result = $packer->unpack($result);
            $data   = $packer->checkData($result);
        } catch (\Throwable $throwable) {
            if (empty($fallback)) {
                throw $throwable;
            }
            $data = PhpHelper::call($fallback, $params);
        }

        return $data;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return ResultInterface
     * @throws RpcClientException
     */
    function __call(string $name, array $arguments)
    {
        $method = $name;
        $prefix = self::DEFER_PREFIX;
        if (strpos($name, $prefix) !== 0) {
            throw new RpcClientException(sprintf('the method of %s is not exist! ', $name));
        }

        if ($name == $prefix) {
            $method = array_shift($arguments);
        } elseif (strpos($name, $prefix) === 0) {
            $method = lcfirst(ltrim($name, $prefix));
        }

        return $this->deferCall($method, $arguments);
    }

    /**
     * Do call service
     *
     * @param string $func
     * @param array  $params
     *
     * @throws \Throwable
     * @return ResultInterface
     */
    private function deferCall(string $func, array $params)
    {
        $profileKey = $this->interface . '->' . $func;
        $fallback   = $this->getFallbackHandler($func);

        try {
            $connectPool    = $this->getPool();
            $circuitBreaker = $this->getBreaker();

            /* @var ConnectionInterface $connection */
            $connection = $connectPool->getConnection();

            $packer   = service_packer();
            $type     = $this->getPackerName();
            $data     = $packer->formatData($this->interface, $this->version, $func, $params);
            $packData = $packer->pack($data, $type);

            $result = $circuitBreaker->call([$connection, 'send'], [$packData], $fallback);

            if ($result === null || $result === false) {
                return null;
            }
        } catch (\Throwable $throwable) {
            if (empty($fallback)) {
                throw $throwable;
            }

            $connection = null;
            $result     = PhpHelper::call($fallback, $params);
        }

        return $this->getResult($connection, $profileKey, $result);
    }

    /**
     * @param ConnectionInterface $connection
     * @param string              $profileKey
     * @param mixed               $result
     *
     * @return ResultInterface
     */
    private function getResult(ConnectionInterface $connection = null, string $profileKey = '', $result = null)
    {
        if (App::isCoContext()) {
            $serviceCoResult = new ServiceCoResult($result, $connection, $profileKey);
            $serviceCoResult->setFallbackData($result);

            return $serviceCoResult;
        }

        return new ServiceDataResult($result, $connection, $profileKey);
    }

    /**
     * @return CircuitBreaker
     */
    private function getBreaker()
    {
        if (empty($this->breakerName)) {
            return \breaker($this->name);
        }

        return \breaker($this->breakerName);
    }

    /**
     * @return PoolInterface
     */
    private function getPool()
    {
        if (empty($this->poolName)) {
            return App::getPool($this->name);
        }

        return App::getPool($this->poolName);
    }

    /**
     * @return string
     */
    private function getPackerName()
    {
        if (empty($this->packerName)) {
            return "";
        }

        return $this->packerName;
    }

    /**
     * @param string $method
     *
     * @return array|null
     */
    private function getFallbackHandler(string $method)
    {
        if (empty($this->fallback)) {
            return null;
        }

        $fallback   = \fallback($this->fallback);
        $interfaces = class_implements(static::class);
        foreach ($interfaces as $interface) {
            if (is_subclass_of($fallback, $interface)) {
                return [$fallback, $method];
            }
        }

        App::warning(sprintf('The %s class does not implement the %s interface', get_parent_class($fallback), JsonHelper::encode($interfaces, JSON_UNESCAPED_UNICODE)));

        return null;
    }
}