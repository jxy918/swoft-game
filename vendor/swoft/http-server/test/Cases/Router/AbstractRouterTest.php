<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/19
 * Time: 上午12:14
 */

namespace SwoftTest\HttpServer;

use PHPUnit\Framework\TestCase;
use Swoft\Http\Server\Router\AbstractRouter;

/**
 * @covers AbstractRouter
 * @author inhere <in.798@qq.com>
 */
class AbstractRouterTest extends TestCase
{
    public function testValidateArguments()
    {
        $stub = $this->getMockForAbstractClass(AbstractRouter::class);
        $ret = $stub->validateArguments('get', 'handler0');

        $this->assertEquals($ret, ['GET']);

        $this->expectException(\InvalidArgumentException::class);
        $stub->validateArguments(null, null);
        $stub->validateArguments('get', null);
    }

    public function testStaticRouteCheck()
    {
        $ret = AbstractRouter::isStaticRoute('/abc');
        $this->assertTrue($ret);

        $ret = AbstractRouter::isStaticRoute('/hi/{name}');
        $this->assertFalse($ret);

        $ret = AbstractRouter::isStaticRoute('/hi/[tom]');
        $this->assertFalse($ret);
    }

    public function testParseParamRoute()
    {
        $stub = $this->getMockForAbstractClass(AbstractRouter::class);

        // 抽象方法才需要配置
        // $stub->expects($this->any())
        //     ->method('parseParamRoute')
        //     ->will($this->returnValue('foo'));

        $conf = [
            'handler' => 'some_handler'
        ];

        $ret = $stub->parseParamRoute('/im/{name}/{age}', [], $conf);
        $this->assertCount(2, $ret);
        $this->assertEquals('im', $ret[0]);// first node
        $this->assertArrayHasKey('start', $ret[1]);
        $this->assertEquals('/im/', $ret[1]['start']);
        $this->assertArrayNotHasKey('include', $ret[1]);

        $ret = $stub->parseParamRoute('/path/to/{name}', [], $conf);
        $this->assertCount(2, $ret);
        $this->assertEquals('path', $ret[0]);
        $this->assertArrayHasKey('start', $ret[1]);
        $this->assertEquals('/path/to/', $ret[1]['start']);
        $this->assertArrayNotHasKey('include', $ret[1]);

        $ret = $stub->parseParamRoute('/hi/{name}', [], $conf);
        $this->assertCount(2, $ret);
        $this->assertEquals('hi', $ret[0]);
        $this->assertArrayHasKey('start', $ret[1]);
        $this->assertArrayNotHasKey('include', $ret[1]);

        $ret = $stub->parseParamRoute('/hi[/{name}]', [], $conf);
        $this->assertNull($ret[0]);
        $this->assertArrayHasKey('include', $ret[1]);
        $this->assertArrayNotHasKey('start', $ret[1]);

        $ret = $stub->parseParamRoute('/hi[/tom]', [], $conf);
        $this->assertNull($ret[0]);
        $this->assertArrayHasKey('include', $ret[1]);
        $this->assertArrayNotHasKey('start', $ret[1]);

        $ret = $stub->parseParamRoute('/hi/[tom]', [], $conf);
        $this->assertEquals('hi', $ret[0]);
        $this->assertArrayHasKey('start', $ret[1]);
        $this->assertArrayNotHasKey('include', $ret[1]);

        $ret = $stub->parseParamRoute('/{category}', [], $conf);
        $this->assertNull($ret[0]);
        $this->assertNull($ret[1]['include']);
        $this->assertArrayHasKey('include', $ret[1]);
        $this->assertArrayNotHasKey('start', $ret[1]);

        $ret = $stub->parseParamRoute('/blog-{category}', [], $conf);
        $this->assertNull($ret[0]);
        $this->assertEquals('/blog-', $ret[1]['include']);
        $this->assertArrayHasKey('include', $ret[1]);
        $this->assertArrayNotHasKey('start', $ret[1]);

        // var_dump($ret);die;
    }
}

