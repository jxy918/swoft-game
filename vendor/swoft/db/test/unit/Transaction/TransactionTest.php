<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit\Transaction;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Stdlib\Helper\Str;
use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\Count4;
use SwoftTest\Db\Testing\Entity\User;
use SwoftTest\Db\Testing\Entity\User4;
use SwoftTest\Db\Unit\TestCase;

/**
 * Class TransactionTest
 *
 * @since 2.0
 */
class TransactionTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testCommit()
    {
        $id  = $this->addRecord();
        $id2 = $this->addRecord();

        $name  = uniqid();
        $name2 = uniqid();

        DB::beginTransaction();
        $result = User::updateOrInsert(['id' => $id], ['name' => $name]);
        $this->assertTrue($result);

        $result2 = User::updateOrInsert(['id' => $id2], ['name' => $name2]);
        $this->assertTrue($result2);
        DB::commit();

        /* @var User $user */
        $user = User::find($id);

        /* @var User $user2 */
        $user2 = User::find($id2);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($name2, $user2->getName());
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testCommitByNest()
    {
        $id  = $this->addRecord();
        $id2 = $this->addRecord();

        $name  = uniqid();
        $name2 = uniqid();

        DB::beginTransaction();
        $result = User::updateOrInsert(['id' => $id], ['name' => $name]);
        $this->assertTrue($result);

        DB::beginTransaction();
        $result2 = User::updateOrInsert(['id' => $id2], ['name' => $name2]);
        $this->assertTrue($result2);
        DB::commit();

        DB::commit();

        /* @var User $user */
        $user = User::find($id);

        /* @var User $user2 */
        $user2 = User::find($id2);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($name2, $user2->getName());
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testRollback()
    {
        $id  = $this->addRecord();
        $id2 = $this->addRecord();

        $name  = uniqid();
        $name2 = uniqid();

        DB::beginTransaction();
        $result = User::updateOrInsert(['id' => $id], ['name' => $name]);
        $this->assertTrue($result);

        $result2 = User::updateOrInsert(['id' => $id2], ['name' => $name2]);
        $this->assertTrue($result2);
        DB::rollBack();

        /* @var User $user */
        $user = User::find($id);

        /* @var User $user2 */
        $user2 = User::find($id2);

        $this->assertNotEquals($name, $user->getName());
        $this->assertNotEquals($name2, $user2->getName());
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testRollbackByNest()
    {
        $id  = $this->addRecord();
        $id2 = $this->addRecord();

        $name  = uniqid();
        $name2 = uniqid();

        DB::beginTransaction();
        $result = User::updateOrInsert(['id' => $id], ['name' => $name]);
        $this->assertTrue($result);

        DB::beginTransaction();
        $result2 = User::updateOrInsert(['id' => $id2], ['name' => $name2]);
        $this->assertTrue($result2);
        DB::commit();

        DB::rollBack();

        /* @var User $user */
        $user = User::find($id);

        /* @var User $user2 */
        $user2 = User::find($id2);

        $this->assertNotEquals($name, $user->getName());
        $this->assertNotEquals($name2, $user2->getName());
    }

    public function testTransaction()
    {
        $id  = $this->addRecord();
        $id2 = $this->addRecord();

        DB::beginTransaction();
        $result = User::updateOrInsert(['id' => $id], ['name' => 'sakura1']);
        $this->assertTrue($result);
        $result2 = User::updateOrInsert(['id' => $id2], ['name' => 'sakura2']);
        $this->assertTrue($result2);
        DB::commit();
    }

    public function testRelationship()
    {

        $name = uniqid();
        $time = time();
        User::truncate();
        Count::truncate();

        DB::beginTransaction();

        $userId = $this->addRecord();

        $countId = $this->addCountRecord();

        $count = Count::updateOrCreate(['id' => $countId], ['create_time' => $time, 'user_id' => $userId]);
        $this->assertEquals($userId, $count->getUserId());
        $this->assertEquals($time, $count->getCreateTime());

        $findCount = Count::find($countId);
        $this->assertEquals($userId, $findCount->getUserId());
        $this->assertEquals($time, $findCount->getCreateTime());

        $whereCount = Count::where('user_id', $userId)->first();
        $this->assertEquals($userId, $whereCount->getUserId());
        $this->assertEquals($time, $whereCount->getCreateTime());

        DB::beginTransaction();
        $res = User::where('id', $userId)->update(['name' => $name]);
        $this->assertEquals(1, $res);
        $userId = $this->addRecord();
        DB::commit();


        DB::rollBack();

        $userDoesnt = User::where('id', $userId)->doesntExist();
        $this->assertTrue($userDoesnt);

        $countDoesnt = Count::where('id', $countId)->doesntExist();
        $this->assertFalse($countDoesnt);
    }

    public function testRelationship2()
    {

        $name = uniqid();
        $time = time();
        Count::truncate();
        User::truncate();

        DB::beginTransaction();

        $userId = $this->addRecord();

        $countId = $this->addCountRecord($userId);
        $count   = Count::updateOrCreate(['id' => $countId], [
            'create_time' => $time,
            'user_id'     => $userId,
            'attributes'  => $name,
        ]);
        $this->assertEquals($userId, $count->getUserId());
        $this->assertEquals($time, $count->getCreateTime());
        $this->assertEquals($name, $count->getAttributes());

        $findCount = Count::find($countId);
        $this->assertEquals($userId, $findCount->getUserId());
        $this->assertEquals($time, $findCount->getCreateTime());
        $this->assertEquals($name, $findCount->getAttributes());

        $whereCount = Count::where('user_id', $userId)->first();
        $this->assertEquals($userId, $whereCount->getUserId());
        $this->assertEquals($time, $whereCount->getCreateTime());
        $this->assertEquals($name, $whereCount->getAttributes());

        // Transaction two
        DB::beginTransaction();
        $res = User::where('id', $userId)->update(['name' => $name]);
        $this->assertEquals(1, $res);
        $this->assertEquals($name, User::find($userId)->getName());

        $userId = $this->addRecord();
        DB::rollBack();


        DB::commit();

        $userDoesnt = User::where('id', $userId)->doesntExist();
        $this->assertTrue($userDoesnt);

        $countDoesnt = Count::where('id', $countId)->exists();
        $this->assertTrue($countDoesnt);
    }

    public function testMultiPool()
    {
        DB::beginTransaction();

        // db.pool4

        /* @var User $user */
        $user = User4::new();
        $user->setAge(mt_rand(1, 100));
        $user->setUserDesc('desc');

        // Save result
        $result = $user->save();
        $this->assertTrue($result);

        $uid4 = $user->getId();

        // db.pool
        $count = Count4::new();
        $count->setUserId($uid4);
        $count->setCreateTime(time());
        $count->setAttributes(Str::random());

        // Save result
        $result = $count->save();
        $this->assertTrue($result);

        $cid = $count->getId();
        $uid = $this->addRecord();

        DB::rollBack();

        $u4 = User4::find($uid4);

        $this->assertIsArray($u4->toArray());

        $c = Count4::find($cid);
        $u = User::find($uid);

        $this->assertTrue(empty($c));
        $this->assertTrue(empty($u));

    }
}
