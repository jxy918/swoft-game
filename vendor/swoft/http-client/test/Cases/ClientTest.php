<?php

namespace SwoftTest\HttpClient;

use Swoft\HttpClient\Client;
use Swoft\HttpClient\Adapter;


/**
 * @uses      ClientTest
 * @version   2018年02月26日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ClientTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function result()
    {
        $request = function () {
            $client = new Client();
            return $client->request('GET', '', [
                'base_uri' => 'http://echo.swoft.org',
            ]);
        };
        $this->assertEquals($request()->getResponse()->getBody()->getContents(), $request()->getResult());
    }

    /**
     * @test
     */
    public function adapters()
    {
        go(function () {
            $client = new Client([
                'adapter' => 'co'
            ]);
            $this->assertInstanceOf(Adapter\CoroutineAdapter::class, $client->getAdapter());
            $client = new Client([
                'adapter' => 'coroutine'
            ]);
            $this->assertInstanceOf(Adapter\CoroutineAdapter::class, $client->getAdapter());
            $client = new Client([
                'adapter' => 'swoole'
            ]);
            $this->assertInstanceOf(Adapter\CoroutineAdapter::class, $client->getAdapter());
        });
        $client = new Client([
            'adapter' => 'curl'
        ]);
        $this->assertInstanceOf(Adapter\CurlAdapter::class, $client->getAdapter());
        $client = new Client([
            'adapter' => 'php'
        ]);
        $this->assertInstanceOf(Adapter\CurlAdapter::class, $client->getAdapter());
    }

    /**
     * @test
     */
    public function addAdapters()
    {
        $client = new Client();
        $client->addAdapter('test', Adapter\CurlAdapter::class);
        $client->setAdapter('test');
        $this->assertInstanceOf(Adapter\CurlAdapter::class, $client->getAdapter());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function adapterNotExist()
    {
        $client = new Client();
        $client->setAdapter('notExistAdapter');
    }

}