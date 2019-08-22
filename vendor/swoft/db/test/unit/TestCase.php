<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Stdlib\Helper\Str;
use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\User;
use Swoole\Event;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function addRecord(): int
    {
        /* @var User $user */
        $user = User::new();
        $user->setAge(mt_rand(1, 100));
        $user->setUserDesc('desc');

        // Save result
        $result = $user->save();
        $this->assertTrue($result);

        return $user->getId();
    }

    /**
     * @param null $userId
     *
     * @return null|int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function addCountRecord($userId = null)
    {
        $count = Count::new();
        $count->setUserId($userId ?: $this->addRecord());
        $count->setCreateTime(time());
        $count->setAttributes(Str::random());
        // Save result
        $result = $count->save();
        $this->assertTrue($result);

        return $count->getId();
    }
}
