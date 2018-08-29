<?php

namespace SwoftTest\Cache;

use Swoft\Cache\Cache;

class CacheTest extends AbstractTestCase
{

    /**
     * @test
     * @requires extention redis
     */
    public function cache()
    {
        $cache = new Cache();
        $key = 'test:key';
        $stringValue = 'value';
        $intValue = 1;
        $floatValue = 1.234;
        $boolValue = false;
        $arrayValue = ['int' => 1, 'float' => 1.234, 'bool' => true, 'string' => 'value'];

        /**
         * Set & Get
         */
        // string
        $setResult = $cache->set($key, $stringValue);
        $this->assertTrue($setResult);
        $getResult = $cache->get($key);
        $this->assertEquals($stringValue, $getResult);
        // int
        $setResult = $cache->set($key, $intValue);
        $this->assertTrue($setResult);
        $getResult = $cache->get($key);
        $this->assertEquals($intValue, $getResult);
        // float
        $setResult = $cache->set($key, $floatValue);
        $this->assertTrue($setResult);
        $getResult = $cache->get($key);
        $this->assertEquals($floatValue, $getResult);
        // bool
        $setResult = $cache->set($key, $boolValue);
        $this->assertTrue($setResult);
        $getResult = $cache->get($key);
        $this->assertEquals($boolValue, $getResult);

        /**
         * Delete
         */
        $deleteResult = $cache->delete($key);
        $this->assertTrue($deleteResult);
        $getResultAfterDelete = $cache->get($key);
        $this->assertNull($getResultAfterDelete);

        /**
         * clear
         */
        $cache->set($key, $stringValue);
        $clearResult = $cache->clear();
        $this->assertTrue($clearResult);
        $getResultAfterClear = $cache->get($key);
        $this->assertNull($getResultAfterClear);

        /**
         * Has
         */
        $cache->set($key, $stringValue);
        // when exist
        $hasResult = $cache->has($key);
        $this->assertTrue($hasResult);
        // When not exist
        $cache->delete($key);
        $hasResult = $cache->has($key);
        $this->assertFalse($hasResult);

        /**
         * setMultiple & getMultiple
         */
        $multiple = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        $setMulResult = $cache->setMultiple($multiple);
        $this->assertTrue($setMulResult);
        $getMulResult = $cache->getMultiple(['key1', 'key2']);
        $this->assertEquals($multiple, $getMulResult);

    }
}