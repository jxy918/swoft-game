<?php

namespace SwoftTest\Db\Cases\Mysql;

use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Qb;
use Swoft\Db\Query;
use SwoftTest\Db\Cases\AbstractMysqlCase;
use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\Keyword;
use SwoftTest\Db\Testing\Entity\User;

/**
 * BugTest
 */
class BugTest extends AbstractMysqlCase
{
    public function testColumnByKeyword()
    {
        $kw = new Keyword();
        $kw->setAlert(mt_rand(1, 100));
        $kw->setDesc('test');

        $id   = $kw->save()->getResult();
        $user = Keyword::findOne(['id' => $id])->getResult();

        $this->assertEquals($id, $user['id']);
    }

    public function testColumnByKeywordByCo()
    {
        go(function () {
            $this->testColumnByKeyword();
        });
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testJoin(int $uid)
    {
        $data  = Query::table('user', 'u')->leftJoin('count', 'u.id=c.uid', 'c')->condition(['u.id' => $uid])->one()->getResult();
        $data2 = Query::table('user', 'u')->leftJoin('count', ['u.id=c.uid'], 'c')->condition(['u.id' => $uid])->one()->getResult();
        $this->assertEquals($data['id'], $uid);
        $this->assertEquals($data2['id'], $uid);
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testJoinByCo(int $uid)
    {
        go(function () use ($uid) {
            $this->testJoin($uid);
        });
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testQueryCount(int $uid)
    {
        $count = User::query()->count()->getResult();
        $this->assertTrue($count > 0);
    }

    public function testQueryNull()
    {
        $result = User::query()->where('id', 0)->one()->getResult();
        $this->assertEquals($result, null);
    }

    /**
     * @dataProvider relationProider
     * @expectedException \PDOException
     *
     * @param int $uid
     */
    public function testUpdateNotExistField(int $uid)
    {
        User::updateOne(['errorField' => 1], ['id' => $uid])->getResult();
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testUpdateNotExistFieldByCo(int $uid)
    {
        go(function () use ($uid) {
            try {
                $this->testUpdateNotExistField($uid);
            } catch (\Throwable $e) {
                $this->assertEquals(MysqlException::class, get_class($e));
            }
        });
    }

    /**
     * @dataProvider relationProider
     * @expectedException \PDOException
     *
     * @param int $uid
     */
    public function testGetNotExistField(int $uid)
    {
        User::findOne(['id' => $uid], ['fields' => ['NotExistField']]);
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testGetNotExistFieldByCo(int $uid)
    {
        go(function () use ($uid) {
            try {
                $this->testGetNotExistField($uid);
            } catch (\Throwable $e) {
                $this->assertEquals(MysqlException::class, get_class($e));
            }
        });
    }

    public function testCounter()
    {
        $age = mt_rand(90, 100);
        $sex = 1;

        $user = new User();
        $user->setName('name');
        $user->setSex($sex);
        $user->setDesc('this my desc');
        $user->setAge($age);

        $uid = $user->save()->getResult();

        $result = Query::table(User::class)->andWhere('id', $uid)->counter(['age' => -21, 'sex' => 12])->getResult();

        $this->assertEquals(1, $result);

        $user = User::findById($uid)->getResult();
        $this->assertEquals(($age - 21), $user['age']);
        $this->assertEquals(($sex + 12), $user['sex']);


        $age = mt_rand(90, 100);
        $sex = 1;

        $user = new User();
        $user->setName('name');
        $user->setSex($sex);
        $user->setDesc('this my desc');
        $user->setAge($age);

        $uid2   = $user->save()->getResult();
        $result = User::counter(['age' => 12, 'sex' => -3], ['id' => $uid2])->getResult();

        $this->assertEquals(1, $result);

        $user = User::findById($uid2)->getResult();
        $this->assertEquals(($age + 12), $user['age']);
        $this->assertEquals(($sex - 3), $user['sex']);
    }

    public function testCounterByCo()
    {
        go(function () {
            $this->testCounter();
        });
    }

    public function testIsNull()
    {
        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setAge(mt_rand(90, 100));

        $uid = $user->save()->getResult();

        $user = Query::table(User::class)->where('id', $uid)->where('description', null, Qb::IS)->one()->getResult();
        $this->assertEquals($user['id'], $uid);
    }

    public function testIsNullByCo()
    {
        go(function () {
            $this->testIsNull();
        });
    }

    public function testUpdateNotDefault()
    {
        $age = mt_rand(90, 100);

        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setAge($age);
        $user->setDesc('desc');
        $uid = $user->save()->getResult();

        /* @var User $user */
        $user = User::findById($uid, ['fields' => ['id', 'name', 'description']])->getResult();
        $user->setName('new Name');
        $result = $user->update()->getResult();
        $this->assertEquals(1, $result);

        /* @var User $user */
        $user = User::findById($uid)->getResult();
        $this->assertEquals($user['name'], 'new Name');
        $this->assertEquals($user['age'], $age);
        $this->assertEquals($user['desc'], 'desc');

        $user->setDesc('new desc one');
        $result = $user->update()->getResult();
        $this->assertEquals(1, $result);

        /* @var User $user */
        $user = User::findById($uid)->getResult();
        $this->assertEquals($user['name'], 'new Name');
        $this->assertEquals($user['age'], $age);
        $this->assertEquals($user['desc'], 'new desc one');
    }

    public function testUpdateNotDefaultByCo()
    {
        go(function () {
            $this->testUpdateNotDefault();
        });
    }

    public function testLike()
    {
        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setAge(mt_rand(90, 120));
        $user->setDesc('testLikeData');
        $uid = $user->save()->getResult();

        /* @var User $qbUser */
        $qbUser = Query::table(User::class)->where('id', $uid)->andwhere('description', '%tLikeD%', Qb::LIKE)->one()->getResult();
        $this->assertEquals($qbUser['id'], $uid);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $uids
     */
    public function testCon(array $uids)
    {
        $result1 = User::findById($uids[0]);
        $result2 = User::findById($uids[1]);

        $user  = $result1->getResult();
        $user2 = $result2->getResult();

        $this->assertEquals($user['id'], $uids[0]);
        $this->assertEquals($user2['id'], $uids[1]);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $uids
     */
    public function testConByCo(array $uids)
    {
        go(function () use ($uids) {
            $this->testCon($uids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $uids
     */
    public function testFindAll(array $uids)
    {
        $users = User::findAll()->getResult();
        $this->assertGreaterThan(2, count($users));
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testLeftJoin(int $uid)
    {
        $result = Query::table(User::class, 'u')->leftJoin(Count::class, 'u.id=c.uid', 'c')->condition(['u.id' => $uid])->one(['u.*'])->getResult();
        $this->assertEquals($result['id'], $uid);
        $result = Query::table('user', 'u')->leftJoin('count', 'u.id=c.uid', 'c')->condition(['u.id' => $uid])->one(['u.*'])->getResult();
        $this->assertEquals($result['id'], $uid);
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testListType(int $uid)
    {
        /* @var User $user*/
        $user = User::findById($uid)->getResult();
        $userAry = $user->toArray();

        $this->assertTrue(is_int($userAry['age']));
        $this->assertTrue(is_int($userAry['sex']));
        $this->assertTrue(is_string($userAry['desc']));

        $row = Query::table(User::class)->where('id', $uid)->one()->getResult();

        $this->assertTrue(is_int($row['age']));
        $this->assertTrue(is_int($row['sex']));
        $this->assertTrue(is_string($row['description']));

        $rows = Query::table(User::class)->where('id', $uid)->get()->getResult();
        foreach ($rows as $userRow){
            $this->assertTrue(is_int($userRow['age']));
            $this->assertTrue(is_int($userRow['sex']));
            $this->assertTrue(is_string($userRow['description']));
        }
    }

    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testListTypeByCo(int $uid)
    {
        go(function () use ($uid){
            $this->testListType($uid);
        });
    }


}