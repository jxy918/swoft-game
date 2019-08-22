<?php declare(strict_types=1);


namespace Swoft\Rpc\Client\Concern;

use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\Exception\ConnectionPoolException;
use Swoft\Rpc\Client\Connection;
use Swoft\Rpc\Client\Exception\RpcClientException;
use Swoft\Rpc\Client\Exception\RpcResponseException;
use Swoft\Rpc\Client\Pool;
use Swoft\Rpc\Client\ReferenceRegister;
use Swoft\Rpc\Protocol;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class ServiceTrait
 *
 * @since 2.0
 */
trait ServiceTrait
{

    /**
     * @param string $interfaceClass
     * @param string $methodName
     * @param array  $params
     *
     * @return mixed
     * @throws ConnectionPoolException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws RpcClientException
     * @throws RpcResponseException
     */
    protected function __proxyCall(string $interfaceClass, string $methodName, array $params)
    {
        $poolName = ReferenceRegister::getPool(__CLASS__);
        $version  = ReferenceRegister::getVersion(__CLASS__);

        /* @var Pool $pool */
        $pool = BeanFactory::getBean($poolName);

        /* @var Connection $connection */
        $connection = $pool->getConnection();
        $connection->setRelease(true);
        $packet = $connection->getPacket();

        // Ext data
        $ext = $connection->getClient()->getExtender()->getExt();

        $protocol = Protocol::new($version, $interfaceClass, $methodName, $params, $ext);
        $data     = $packet->encode($protocol);
        $message  = sprintf(
            'Rpc call failed.interface=%s method=%s pool=%s version=%s',
            $interfaceClass, $methodName, $poolName, $version
        );

        $result = $this->sendAndRecv($connection, $data, $message);
        $connection->release();

        $response = $packet->decodeResponse($result);
        if ($response->getError() !== null) {
            $code      = $response->getError()->getCode();
            $message   = $response->getError()->getMessage();
            $errorData = $response->getError()->getData();

            throw new RpcResponseException(
                sprintf(
                    'Rpc call error!code=%d message=%s data=%s pool=%s version=%s',
                    $code, $message, JsonHelper::encode($errorData), $poolName, $version
                ),
                $code
            );
        }

        return $response->getResult();

    }

    /**
     * @param Connection $connection
     * @param string     $data
     * @param string     $message
     * @param bool       $reconnect
     *
     * @return string
     * @throws RpcClientException
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function sendAndRecv(Connection $connection, string $data, string $message, bool $reconnect = false): string
    {
        // Reconnect
        if ($reconnect) {
            $connection->reconnect();
        }

        if (!$connection->send($data)) {
            if ($reconnect) {
                throw new RpcClientException($message);
            }

            return $this->sendAndRecv($connection, $data, $message, true);
        }

        $result = $connection->recv();
        if ($result === false || empty($result)) {
            if ($reconnect) {
                throw new RpcClientException($message);
            }

            return $this->sendAndRecv($connection, $data, $message, true);
        }

        return $result;
    }
}