<?php declare(strict_types=1);


namespace SwoftTest\Config\Unit;


use Swoft\Config\Config;

/**
 * Class PhpConfigTest
 *
 * @since 2.0
 */
class PhpConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Set up
     */
    public function setUp()
    {
        $config = new Config();
        $config->setPath(__DIR__ . '/../config-php');
        $config->setEnv('pro');
        $config->init();;

        $this->config = $config;
    }

    public function testData()
    {
        $data = $this->config->getIterator()->getArrayCopy();

        $this->assertEquals($data, $this->getConfigData());
    }

    public function testGet()
    {
        $value2 = $this->config->get('key2');
        $this->assertEquals($value2, 'value2');

        $value2 = $this->config->get('key.key2');
        $this->assertEquals($value2, 'value2Pro');

        $value2 = $this->config->get('data.key3.key1');
        $this->assertEquals($value2, ['key' => 'dataChildKey1']);
    }

    public function testOffsetGet()
    {
        $value2 = $this->config->offsetGet('key2');
        $this->assertEquals($value2, 'value2');

        $value2 = $this->config->offsetGet('key.key2');
        $this->assertEquals($value2, 'value2Pro');

        $value2 = $this->config->offsetGet('data.key3.key1');
        $this->assertEquals($value2, ['key' => 'dataChildKey1']);
    }

    public function testOffsetExists()
    {
        $result = $this->config->offsetExists('data.key3.key9');
        $this->assertFalse($result);

        $result = $this->config->offsetExists('data.key3.key1');
        $this->assertTrue($result);
    }

    /**
     * @expectedException Swoft\Config\Exception\ConfigException
     * @throws \Swoft\Config\Exception\ConfigException
     */
    public function testForget()
    {
        $this->config->forget('');
    }

    /**
     * @expectedException Swoft\Config\Exception\ConfigException
     * @throws \Swoft\Config\Exception\ConfigException
     */
    public function testOffsetUnset()
    {
        $this->config->offsetUnset('');
    }

    /**
     * @expectedException Swoft\Config\Exception\ConfigException
     * @throws \Swoft\Config\Exception\ConfigException
     */
    public function testOffsetSett()
    {
        $this->config->offsetSet('key', 'value');
    }
}