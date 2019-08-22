<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use SwoftTest\Db\Testing\Entity\Count2;
use SwoftTest\Db\Testing\Entity\User;

/**
 * Class SelectorTest
 *
 * @since 2.0
 */
class SelectorTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function testSelector()
    {
        $count = new Count2();
        $count->setUserId(mt_rand(1, 100));
        $count->setAttributes('attrs');
        $count->setCreateTime(time());

        $result = $count->save();

        $this->assertTrue($result);
        $this->assertGreaterThan(0, $count->getId());
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testModelSelect()
    {
        $getId = User::db('test2')->insertGetId([
            'name'      => uniqid(),
            'password'  => md5(uniqid()),
            'age'       => mt_rand(1, 100),
            'user_desc' => 'u desc',
            'foo'       => 'bar',
            'xxxx'      => '223asdf'
        ]);

        $this->assertGreaterThan(0, $getId);

        /* @var User $user */
        $user = User::db('test2')->find($getId);
        $this->assertGreaterThan(0, $user->getAge());
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testQuerySelect()
    {
        $getId = DB::table('user')->db('test2')->insertGetId([
            'name'      => uniqid(),
            'password'  => md5(uniqid()),
            'age'       => mt_rand(1, 100),
            'user_desc' => 'u desc',
        ]);

        $this->assertGreaterThan(0, $getId);

        $user = DB::table('user')->db('test2')->find($getId);
        $this->assertGreaterThan(0, $user['age']);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testDbSelect()
    {
        $getId = DB::db('test2')->insertGetId("INSERT INTO `user` (`id`, `name`, `age`, `password`, `user_desc`, `add`, `hahh`, `test_json`) VALUES (NULL, '', '0', '', '', NULL, '1', NULL);");

        $this->assertGreaterThan(0, $getId);

        $user = DB::db('test2')->selectOne('select * from user where id=?', [$getId]);
        $this->assertGreaterThan(0, $user['id']);

        $user = DB::db('test2')->selectOne('select * from user where id=:id', ['id' => $getId]);
        $this->assertGreaterThan(0, $user['id']);
    }
}
