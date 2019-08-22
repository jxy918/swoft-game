<?php

namespace SwoftTest\Http\Server\Helper;

use PHPUnit\Framework\TestCase;
use Swoft\Http\Server\Helper\RouteHelper;

/**
 * Class RouteHelperTest
 * @package SwoftTest\Http\Server\Helper
 */
class RouteHelperTest extends TestCase
{
    public function testIsStaticRoute(): void
    {
        $ret = RouteHelper::isStaticRoute('/abc');
        $this->assertTrue($ret);

        $ret = RouteHelper::isStaticRoute('/hi/{name}');
        $this->assertFalse($ret);

        $ret = RouteHelper::isStaticRoute('/hi/[tom]');
        $this->assertFalse($ret);
    }

}
