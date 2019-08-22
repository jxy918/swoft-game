<?php declare(strict_types=1);


namespace SwoftTest\Consul\Unit;


use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Consul\KV;
use Swoft\Consul\Response;

class KVTest extends TestCase
{
    /**
     * @var KV
     */
    private $kv;

    /**
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ClientException
     * @throws ServerException
     */
    protected function setUp()
    {
        $this->kv = BeanFactory::getBean(KV::class);
        $this->kv->delete('/test', ['recurse' => true]);
    }

    /**
     * Tear down
     */
    protected function tearDown()
    {
        $this->kv = null;
    }

    /**
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function testSetGetWithDefaultOptions()
    {
        $value    = date('r');
        $response = $this->kv->put('/test/my/key', $value);
        $this->assertTrue($response->getResult());

        $response = $this->kv->get('/test/my/key');
        $this->assertInstanceOf(Response::class, $response);

        $json = $response->getResult();
        $this->assertSame($value, base64_decode($json[0]['Value']));
    }

    /**
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function testSetGetWithRawOption()
    {
        $value = date('r');
        $this->kv->put('/test/my/key2', $value);

        $response = $this->kv->get('/test/my/key2', ['raw' => true]);
        $this->assertInstanceOf(Response::class, $response);

        $body = $response->getBody();
        $this->assertSame($value, $body);
    }

    /**
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function testSetGetWithFlagsOption()
    {
        $flags = mt_rand();
        $this->kv->put('/test/my/key', 'hello', array('flags' => $flags));

        $response = $this->kv->get('/test/my/key');
        $this->assertInstanceOf(Response::class, $response);

        $json = $response->getResult();
        $this->assertSame($flags, $json[0]['Flags']);
    }

    /**
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function testSetGetWithKeysOption()
    {
        $this->kv->put('/test/my/key1', 'hello 1');
        $this->kv->put('/test/my/key2', 'hello 2');
        $this->kv->put('/test/my/key3', 'hello 3');

        $response = $this->kv->get('/test/my', ['keys' => true]);
        $this->assertInstanceOf(Response::class, $response);

        $json = $response->getResult();

        $this->assertSame(array('test/my/key1', 'test/my/key2', 'test/my/key3'), $json);
    }

    /**
     * @expectedException Swoft\Consul\Exception\ClientException
     *
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     * @throws ClientException
     */
    public function testDeleteWithDefaultOptions()
    {
        $this->kv->put('/test/my/key', 'hello');
        $this->kv->get('/test/my/key');
        $this->kv->delete('/test/my/key');

        $this->kv->get('/test/my/key');
    }

    /**
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function testDeleteWithRecurseOption()
    {
        $this->kv->put('/test/my/key1', 'hello 1');
        $this->kv->put('/test/my/key2', 'hello 2');
        $this->kv->put('/test/my/key3', 'hello 3');

        $this->kv->get('/test/my/key1');
        $this->kv->get('/test/my/key2');
        $this->kv->get('/test/my/key3');

        $this->kv->delete('/test/my', ['recurse' => true]);

        for ($i = 1; $i < 3; $i++) {
            try {
                $this->kv->get('/test/my/key' . $i);
            } catch (\Exception $e) {
                $this->assertTrue($e instanceof ClientException);
                $this->assertContains('404', $e->getMessage());
            }
        }
    }
}