<?php declare(strict_types=1);


namespace SwoftTest\Consul\Unit;


use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Catalog;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;

/**
 * Class CatalogTest
 *
 * @since 2.0
 */
class CatalogTest extends TestCase
{
    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function setUp()
    {
        $this->catalog = BeanFactory::getBean(Catalog::class);
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ClientException
     * @throws ServerException
     */
    public function testRegister()
    {
        $id = $this->register('40e4a748-2192-161a-0510-9bf59fe950' . mt_rand(10, 99));

        $node     = [
            'Datacenter' => 'dc1',
            'ID'         => $id,
            'Node'       => 'foobar'
        ];
        $response = $this->catalog->deregister($node);
        $this->assertTrue($response->getResult());
    }

    private function register(string $id): string
    {
        $json = '{
  "Datacenter": "dc1",
  "ID": "' . $id . '",
  "Node": "foobar",
  "Address": "192.168.10.10",
  "TaggedAddresses": {
    "lan": "192.168.10.10",
    "wan": "10.0.10.10"
  },
  "NodeMeta": {
    "somekey": "somevalue"
  },
  "Service": {
    "ID": "redis1",
    "Service": "redis",
    "Tags": [
      "primary",
      "v1"
    ],
    "Address": "127.0.0.1",
    "Meta": {
        "redis_version": "4.0"
    },
    "Port": 8000
  },
  "Check": {
    "Node": "foobar",
    "CheckID": "service:redis1",
    "Name": "Redis health check",
    "Notes": "Script based health check",
    "Status": "passing",
    "ServiceID": "redis1",
    "Definition": {
      "TCP": "localhost:8888",
      "Interval": "5s",
      "Timeout": "1s",
      "DeregisterCriticalServiceAfter": "30s"
    }
  },
  "SkipNodeUpdate": false
}';

        $node     = json_decode($json, true);
        $response = $this->catalog->register($node);
        $this->assertTrue($response->getResult());
        return $id;
    }
}