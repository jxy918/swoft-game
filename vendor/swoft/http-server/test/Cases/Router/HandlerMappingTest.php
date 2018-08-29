<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/19
 * Time: 上午12:12
 */

namespace SwoftTest\HttpServer;

use PHPUnit\Framework\TestCase;
use Swoft\Http\Server\Router\HandlerMapping;

/**
 * @covers HandlerMapping
 * @author inhere <in.798@qq.com>
 */
class HandlerMappingTest extends TestCase
{
    private function createRouter()
    {
        $r = new HandlerMapping();
        $r->get('/', 'handler0');
        $r->get('/test', 'handler1');

        $r->get('/test1[/optional]', 'handler');

        $r->get('/{name}', 'handler2');

        $r->get('/hi/{name}', 'handler3', [
            'params' => [
                'name' => '\w+',
            ]
        ]);

        $r->post('/hi/{name}', 'handler4');
        $r->put('/hi/{name}', 'handler5');

        return $r;
    }

    public function testAddRoutes()
    {
        $router = $this->createRouter();

        $this->assertTrue(4 < $router->count());
        $this->assertCount(2, $router->getStaticRoutes());
    }


    public function testStaticRoute()
    {
        $router = $this->createRouter();

        // 1
        $ret = $router->match('/', 'GET');

        $this->assertCount(3, $ret);

        list($status, $path, $route) = $ret;

        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertSame('/', $path);
        $this->assertSame('handler0', $route['handler']);

    }

    public function testOptionalParamRoute()
    {
        $router = $this->createRouter();

        // route: '/test1[/optional]'
        $ret = $router->match('/test1', 'GET');

        $this->assertCount(3, $ret);

        list($status, , $route) = $ret;

        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertSame('handler', $route['handler']);

        // route: '/test1[/optional]'
        $ret = $router->match('/test1/optional', 'GET');

        $this->assertCount(3, $ret);

        list($status, , $route) = $ret;

        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertSame('handler', $route['handler']);

    }

    public function testParamRoute()
    {
        $router = $this->createRouter();

        // route: /{name}
        $ret = $router->match('/tom', 'GET');

        $this->assertCount(3, $ret);

        list($status, $path, $route) = $ret;

        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertSame('/tom', $path);
        $this->assertSame('/{name}', $route['original']);
        $this->assertSame('handler2', $route['handler']);

        // route: /hi/{name}
        $ret = $router->match('/hi/tom', 'GET');

        $this->assertCount(3, $ret);
// var_dump($ret, $router->getRegularRoutes());die;
        list($status, $path, $route) = $ret;

        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertSame('/hi/tom', $path);
        $this->assertSame('/hi/{name}', $route['original']);
        $this->assertSame('handler3', $route['handler']);
    }

    public function testMethods()
    {
        $router = $this->createRouter();

        // route: /hi/{name}
        $ret = $router->match('/hi/tom', 'post');

        $this->assertCount(3, $ret);

        list($status, , $route) = $ret;
        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertArrayHasKey('name', $route['matches']);
        $this->assertSame('handler4', $route['handler']);

        // route: /hi/{name}
        $ret = $router->match('/hi/tom', 'put');

        list($status, , $route) = $ret;
        $this->assertCount(3, $ret);
        $this->assertSame(HandlerMapping::FOUND, $status);
        $this->assertArrayHasKey('name', $route['matches']);
        $this->assertSame('handler5', $route['handler']);

        // route: /hi/{name}
        $ret = $router->match('/hi/tom', 'delete');

        list($status, , $methods) = $ret;
        $this->assertCount(3, $ret);
        $this->assertSame(HandlerMapping::METHOD_NOT_ALLOWED, $status);
        $this->assertCount(3, $methods);
    }
}
