<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing;


use Swoft\Rpc\Server\Annotation\Mapping\Service;
use SwoftTest\Rpc\Server\Testing\Lib\DemoInterface;

/**
 * Class DemoServiceV2
 *
 * @since 2.0
 *
 * @Service(version="1.1")
 */
class DemoServiceV2 implements DemoInterface
{
    /**
     * @param int    $uid
     * @param string $type
     *
     * @return array
     */
    public function getList(int $uid, string $type): array
    {
        return [
            'name' => 'list',
            'list' => [
                'id'   => $uid,
                'type' => $type,
                'name' => 'name'
            ],
            'v'=> '1.1'
        ];
    }

    /**
     * @param $uid
     *
     * @return array|mixed
     */
    public function getInfo($uid)
    {
        return [
            'name' => 'info',
            'item' => [
                'id'   => $uid,
                'name' => 'name'
            ],
            'v'=> '1.1'
        ];
    }

    /**
     * @return array
     */
    public function notClassMd(): array
    {
        return [];
    }

    public function returnNull(): void
    {
        return ;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool
    {
        if ($id > 100) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function error(): bool
    {
        throw new \Exception('error message 1.1', 324231);
    }
}