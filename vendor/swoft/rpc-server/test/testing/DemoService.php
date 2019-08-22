<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing;

use Swoft\Rpc\Server\Annotation\Mapping\Service;
use SwoftTest\Rpc\Server\Testing\Lib\DemoInterface;

/**
 * Class DemoService
 *
 * @since 2.0
 *
 * @Service()
 */
class DemoService implements DemoInterface
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
            ]
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
            ]
        ];
    }

    /**
     * @return array
     */
    public function notClassMd(): array
    {
        return [];
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

    public function returnNull(): void
    {
        return ;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function error(): bool
    {
        throw new \Exception('error message', 324231);
    }
}