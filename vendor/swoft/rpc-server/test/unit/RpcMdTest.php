<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Unit;


use SwoftTest\Rpc\Server\Testing\Lib\DemoInterface;

class RpcMdTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testAllMd()
    {
        $data     = [
            'name'      => 'list',
            'MethodMd2' => 'MethodMd2',
            'MethodMd3' => 'MethodMd3',
            'MethodMd'  => 'MethodMd',
            'ClassMd2'  => 'ClassMd2',
            'ClassMd3'  => 'ClassMd3',
            'ClassMd'   => 'ClassMd',
            'userMd'    => 'userMd'
        ];
        $response = $this->mockRpcServer->call(DemoInterface::class, 'getList', [12, 'type2'], [], '1.3');
        $this->assertEquals($data, $response->getData());
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testOneMd()
    {
        $data     = [
            'name'      => 'info',
            'MethodMd2' => 'MethodMd2',
            'ClassMd2'  => 'ClassMd2',
            'ClassMd3'  => 'ClassMd3',
            'ClassMd'   => 'ClassMd',
            'userMd'    => 'userMd'
        ];
        $response = $this->mockRpcServer->call(DemoInterface::class, 'getInfo', [12], [], '1.3');
        $this->assertEquals($data, $response->getData());
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     * @throws \Swoft\Rpc\Exception\RpcException
     */
    public function testNotClassMd()
    {
        $data = [
            'name'     => 'notClassMd',
            'ClassMd2' => 'ClassMd2',
            'ClassMd3' => 'ClassMd3',
            'ClassMd'  => 'ClassMd',
            'userMd'   => 'userMd'
        ];

        $response = $this->mockRpcServer->call(DemoInterface::class, 'notClassMd', [12], [], '1.3');
        $this->assertEquals($data, $response->getData());
    }
}