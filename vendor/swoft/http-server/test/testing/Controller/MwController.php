<?php declare(strict_types=1);

namespace SwoftTest\Http\Server\Testing\Controller;

use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use SwoftTest\Http\Server\Testing\Middleware\ControllerMd;
use SwoftTest\Http\Server\Testing\Middleware\ControllerMd2;
use SwoftTest\Http\Server\Testing\Middleware\ControllerMd3;
use SwoftTest\Http\Server\Testing\Middleware\MethodMd;
use SwoftTest\Http\Server\Testing\Middleware\MethodMd2;
use SwoftTest\Http\Server\Testing\Middleware\MethodMd3;

/**
 * Class MwController
 *
 * @since 2.0
 *
 * @Controller("testMw")
 * @Middlewares({
 *     @Middleware(ControllerMd::class),
 *     @Middleware(ControllerMd3::class),
 * })
 * @Middleware(ControllerMd2::class)
 */
class MwController
{
    /**
     * @RequestMapping(route="method")
     * @Middlewares({
     *     @Middleware(MethodMd::class),
     *     @Middleware(MethodMd3::class),
     * })
     * @Middleware(MethodMd2::class)
     */
    public function method()
    {
        return ['method'];
    }

    /**
     * @RequestMapping(route="method2")
     *
     * @Middleware(MethodMd2::class)
     */
    public function method2()
    {
        return ['method2'];
    }

    /**
     * @RequestMapping(route="method3")
     */
    public function method3()
    {
        return ['method3'];
    }
}
