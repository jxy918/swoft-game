<?php declare(strict_types=1);


namespace SwoftTest\Consul\Unit;


use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Agent;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use SwoftTest\Db\Unit\TestCase;

/**
 * Class AgentTest
 *
 * @since 2.0
 */
class AgentTest extends TestCase
{
    /**
     * @var Agent
     */
    private $agent;

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function setUp()
    {
        $this->agent = BeanFactory::getBean(Agent::class);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ClientException
     * @throws ServerException
     */
    public function testRegisterService()
    {
        $serviceId = $this->registerService('testRegisterService');
        $response  = $this->agent->deregisterService($serviceId);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testServices()
    {
        $serviceId = $this->registerService('testServices');
        $response  = $this->agent->services();

        $serviceInfo = $response->getResult()[$serviceId];
        $this->assertNotEmpty($serviceInfo);

        $this->agent->deregisterService($serviceId);
    }

    /**
     * @param string $serviceId
     *
     * @return string
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ClientException
     * @throws ServerException
     */
    private function registerService(string $serviceId): string
    {
        $json     = '{
  "ID": "' . $serviceId . '",
  "Name": "' . $serviceId . '",
  "Tags": [
    "primary",
    "v1"
  ],
  "Address": "127.0.0.1",
  "Port": 8000,
  "Meta": {
    "redis_version": "4.0"
  },
  "EnableTagOverride": false,
  "Weights": {
    "Passing": 10,
    "Warning": 1
  }
}';
        $serivce  = json_decode($json, true);
        $response = $this->agent->registerService($serivce);
        $this->assertEquals($response->getStatusCode(), 200);

        return $serviceId;
    }
}