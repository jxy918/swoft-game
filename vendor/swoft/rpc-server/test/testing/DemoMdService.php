<?php declare(strict_types=1);

namespace SwoftTest\Rpc\Server\Testing;

use SwoftTest\Rpc\Server\Testing\Lib\DemoInterface;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use Swoft\Rpc\Server\Annotation\Mapping\Middlewares;
use Swoft\Rpc\Server\Annotation\Mapping\Middleware;
use SwoftTest\Rpc\Server\Testing\Middleware\ClassMd;
use SwoftTest\Rpc\Server\Testing\Middleware\ClassMd2;
use SwoftTest\Rpc\Server\Testing\Middleware\ClassMd3;
use SwoftTest\Rpc\Server\Testing\Middleware\MethodMd;
use SwoftTest\Rpc\Server\Testing\Middleware\MethodMd2;
use SwoftTest\Rpc\Server\Testing\Middleware\MethodMd3;

/**
 * Class DemoMdService
 *
 * @since 2.0
 *
 * @Service(version="1.3")
 *
 * @Middlewares({
 *     @Middleware(ClassMd::class),
 *     @Middleware(ClassMd3::class)
 * })
 * @Middleware(ClassMd2::class,)
 */
class DemoMdService implements DemoInterface
{
    /**
     * @Middlewares({
     *     @Middleware(MethodMd::class),
     *     @Middleware(MethodMd3::class)
     * })
     *
     * @Middleware(MethodMd2::class)
     *
     * @param int    $uid
     * @param string $type
     *
     * @return array
     */
    public function getList(int $uid, string $type): array
    {
        return ['name' => 'list'];
    }

    /**
     * @Middleware(MethodMd2::class)
     *
     * @param $uid
     *
     * @return array
     */
    public function getInfo($uid)
    {
        return ['name' => 'info'];
    }

    /**
     * @return array
     */
    public function notClassMd(): array
    {
        return [
            'name' => 'notClassMd'
        ];
    }

    public function returnNull(): void
    {
        return ;
    }

    public function delete(int $id): bool
    {
        return true;
    }

    public function error(): bool
    {
        return false;
    }
}