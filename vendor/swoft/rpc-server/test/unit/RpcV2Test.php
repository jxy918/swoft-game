<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Unit;

use SwoftTest\Rpc\Server\Testing\Lib\DemoInterface;

class RpcV2Test extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testGetList()
    {
        $list = [
            'name' => 'list',
            'list' => [
                'id'   => 12,
                'type' => 'type2',
                'name' => 'name'
            ],
            'v'=> '1.1'
        ];

        $response = $this->mockRpcServer->call(DemoInterface::class, 'getList', [12, 'type2'], [], '1.1');
        $response->assertSuccess();
        $response->assertEqualJsonResult($list);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testGetInfo()
    {
        $info = [
            'name' => 'info',
            'item' => [
                'id'   => 12,
                'name' => 'name'
            ],
            'v'=> '1.1'
        ];

        $response = $this->mockRpcServer->call(DemoInterface::class, 'getInfo', [12], [], '1.1');
        $response->assertSuccess();
        $response->assertEqualJsonResult($info);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testGetDelete()
    {
        $response = $this->mockRpcServer->call(DemoInterface::class, 'delete', [12], [], '1.1');
        $response->assertSuccess();
        $response->assertEqualResult(false);

        $response = $this->mockRpcServer->call(DemoInterface::class, 'delete', [122], [], '1.1');
        $response->assertSuccess();
        $response->assertEqualResult(true);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testCallErro()
    {
        $response = $this->mockRpcServer->call(DemoInterface::class, 'delete', [], [], '1.1');
        $response->assertFail();
        $response->assertErrorCode(0);
        $response->assertContainErrorMessage('Too few arguments to function');
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testException()
    {

        $response = $this->mockRpcServer->call(DemoInterface::class, 'error', [], [], '1.1');
        $response->assertFail();
        $response->assertErrorCode(324231);
        $response->assertErrorMessage('error message 1.1');
    }
}