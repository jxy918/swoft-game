<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace SwoftTest\Db\Cases\Mysql;

use Swoft\Db\Query;
use SwoftTest\Db\Cases\AbstractMysqlCase;
use SwoftTest\Db\Testing\Entity\OtherUser;
use SwoftTest\Db\Testing\Entity\User;

/**
 * QueryTest
 */
class QueryBuilderTest extends AbstractMysqlCase
{
    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbSelect(int $id)
    {
        $result = Query::table(User::class)->where('id', $id)->one()->getResult();
        $this->assertEquals($id, $result['id']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbSelectByCo(int $id)
    {
        go(function () use ($id) {
            $this->testDbSelect($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbDelete(int $id)
    {
        $result = Query::table(User::class)->where('id', $id)->delete()->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbDeleteByCo(int $id)
    {
        go(function () use ($id) {
            $this->testDbDelete($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbUpdate(int $id)
    {
        $result = Query::table(User::class)->where('id', $id)->update(['name' => 'name666'])->getResult();
        $user   = User::findById($id)->getResult();
        $this->assertEquals('name666', $user['name']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDbUpdateByCo(int $id)
    {
        go(function () use ($id) {
            $this->testDbUpdate($id);
        });
    }

    public function testDbInsert()
    {
        $values = [
            'name'        => 'name',
            'sex'         => 1,
            'description' => 'this my desc',
            'age'         => 99,
        ];
        $result = Query::table(User::class)->insert($values)->getResult();
        $user   = User::findById($result)->getResult();
        $this->assertCount(5, $user);
    }

    public function testDbInsertByCo()
    {
        go(function () {
            $this->testDbInsert();
        });
    }

    public function testSelectDb()
    {
        $data   = [
            'name'        => 'name',
            'sex'         => 1,
            'description' => 'this my desc table',
            'age'         => mt_rand(1, 100),
        ];
        $userid = Query::table(User::class)->selectDb('test2')->insert($data)->getResult();

        $user  = User::findById($userid)->getResult();
        $user2 = Query::table(User::class)->selectDb('test2')->where('id', $userid)->one()->getResult();

        $this->assertEquals($user2['description'], 'this my desc table');
        $this->assertEquals($user2['id'], $userid);
    }

    public function testSelectDbByCo()
    {
        go(function () {
            $this->testSelectDb();
        });
    }

    public function testSelectTable()
    {
        $data   = [
            'name'        => 'name',
            'sex'         => 1,
            'description' => 'this my desc',
            'age'         => mt_rand(1, 100),
        ];
        $result = Query::table('user2')->insert($data)->getResult();
        $user2  = Query::table('user2')->where('id', $result)->one()->getResult();
        $this->assertEquals($user2['id'], $result);
    }

    public function testSelectTableByCo()
    {
        go(function () {
            $this->testSelectTable();
        });
    }

    public function testSelectinstance()
    {
        $data   = [
            'name'        => 'name',
            'sex'         => 1,
            'description' => 'this my desc instance',
            'age'         => mt_rand(1, 100),
        ];
        $userid = Query::table(User::class)->selectInstance('other')->insert($data)->getResult();

        $user  = OtherUser::findById($userid)->getResult();
        $user2 = Query::table(User::class)->selectInstance('other')->where('id', $userid)->one()->getResult();
        $this->assertEquals($user2['description'], 'this my desc instance');
        $this->assertEquals($user2['id'], $userid);
    }

    public function testSelectinstanceByCo()
    {
        go(function () {
            $this->testSelectinstance();
        });
    }

    public function testCondtionAndByF1()
    {
        $age    = mt_rand(1, 100);
        $data   = [
            'name'        => 'nameQuery',
            'sex'         => 1,
            'description' => 'this my desc instance',
            'age'         => $age,
        ];
        $userid = Query::table(User::class)->insert($data)->getResult();
        $user   = Query::table(User::class)->condition(['name' => 'nameQuery', 'age' => $age])->one()->getResult();

        $this->assertEquals('nameQuery', $user['name']);
        $this->assertEquals($age, $user['age']);

        $user2 = Query::table(User::class)->where('id', $userid)->condition(['name' => 'nameQuery', 'age' => $age])->one()->getResult();
        $this->assertEquals('nameQuery', $user2['name']);
        $this->assertEquals($age, $user2['age']);
    }

    public function testCondtionAndByF1ByCo()
    {
        go(function () {
            $this->testCondtionAndByF1();
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion2AndByF1(array $ids)
    {
        $users = Query::table(User::class)->condition(['sex' => 1, 'id' => $ids])->get()->getResult();
        $this->assertCount(2, $users);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion2AndByF1ByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testCondtion2AndByF1($ids);
        });
    }

    public function testCondtion1AndByF3()
    {
        $age  = mt_rand(1, 100);
        $data = [
            'name'        => 'nameQuery',
            'sex'         => 1,
            'description' => 'this my desc instance',
            'age'         => $age - 1,
        ];

        $userid = Query::table(User::class)->insert($data)->getResult();
        $user   = Query::table(User::class)->condition(['age', '<', $age])->andWhere('id', $userid)->orderBy('id', 'desc')->one()->getResult();
        $this->assertEquals($userid, $user['id']);
    }

    public function testCondtion1AndByF3ByCo()
    {
        go(function () {
            $this->testCondtion1AndByF3();
        });
    }

    public function testCondtion2AndByF3()
    {
        $age  = mt_rand(1, 100);
        $data = [
            'name'        => 'testCondtion2AndByF3',
            'sex'         => 1,
            'description' => 'this my desc instance',
            'age'         => $age - 1,
        ];

        $userid = Query::table(User::class)->insert($data)->getResult();
        $users  = Query::table(User::class)->condition(['id', 'between', $userid - 1, $userid + 1])->orderBy('id', 'desc')->get()->getResult();
        $this->assertTrue(count($users) > 1);
    }

    public function testCondtion2AndByF3ByCo()
    {
        go(function () {
            $this->testCondtion2AndByF3();
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testCondtion3AndByF3(int $id)
    {
        $age  = mt_rand(1, 100);
        $data = [
            'name'        => 'nameQuery',
            'sex'         => 1,
            'description' => 'this my desc instance',
            'age'         => $age - 1,
        ];

        $userid = Query::table(User::class)->insert($data)->getResult();
        $users  = Query::table(User::class)->condition(['age', 'not between', $age, $age + 1])->orderBy('id', 'desc')->get()->getResult();

        $this->assertTrue(count($users) > 1);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testCondtion3AndByF3ByCo(int $id)
    {
        go(function () use ($id) {
            $this->testCondtion3AndByF3($id);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion4AndByF3(array $ids)
    {
        $users = Query::table(User::class)->condition(['id', 'in', $ids])->orderBy('id', 'desc')->get()->getResult();

        $this->assertCount(2, $users);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion4AndByF3ByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testCondtion4AndByF3($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion5AndByF3(array $ids)
    {
        $users = Query::table(User::class)->condition(['id', 'not in', $ids])->orderBy('id', 'desc')->get()->getResult();

        $this->assertTrue(count($users) > 2);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion5AndByF3ByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testCondtion5AndByF3($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testLimit(array $ids)
    {
        sort($ids);
        $result = Query::table(User::class)->whereIn('id', $ids)->orderBy('id', 'asc')->limit(1, 1)->get()->getResult();
        $this->assertEquals($ids[1], $result[0]['id']);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testLimitByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testLimit($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion6(array $ids)
    {
        $users = Query::table(User::class)->condition(['name' => 'name', 'id' => $ids, ['name' => 'name']])->get()->getResult();
        foreach ($users as $user) {
            $this->assertTrue(in_array($user['id'], $ids));
        }

        $users = Query::table(User::class)->condition(['id' => $ids, ['age', 'between', 0, 1000], ['name' => 'name']])->get()->getResult();
        foreach ($users as $user) {
            $this->assertTrue(in_array($user['id'], $ids));
        }

        $users = Query::table(User::class)->condition([['age', 'between', 0, 1000], 'id' => $ids, 'name' => 'name'])->get()->getResult();
        foreach ($users as $user) {
            $this->assertTrue(in_array($user['id'], $ids));
        }

        $users = Query::table(User::class)->condition([['age', 'between', 0, 1000], ['id' => $ids], ['name' => 'name']])->get()->getResult();
        foreach ($users as $user) {
            $this->assertTrue(in_array($user['id'], $ids));
        }

        $users = Query::table(User::class)->condition([['id' => $ids]])->get()->getResult();
        foreach ($users as $user) {
            $this->assertTrue(in_array($user['id'], $ids));
        }

        $users = Query::table(User::class)->condition([])->get()->getResult();
        $this->assertGreaterThan(2, $users);

        $users = Query::table(User::class)->condition(['id', 'not in', []])->get()->getResult();
        $this->assertGreaterThan(2, $users);

        $users = Query::table(User::class)->condition(['id', 'in', []])->get()->getResult();
        $this->assertGreaterThan(2, $users);

        $users = Query::table(User::class)->condition(['id' => []])->get()->getResult();
        $this->assertGreaterThan(2, $users);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCondtion6ByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testCondtion6($ids);
        });
    }

    public function testCondionLikeOrNotLike(){

        $name = uniqid();
        $values = [
            'name'        => $name,
            'sex'         => 1,
            'description' => 'this my desc',
            'age'         => 99,
        ];

        $userid = Query::table(User::class)->insert($values)->getResult();
        $user   = Query::table(User::class)->condition(['name', 'like', '%' . $name . '%'])->one()->getResult();
        $this->assertEquals($user['id'], $userid);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testModelQuerySelectOne(int $id)
    {
        $result = Query::table(User::class)->where('id', $id)->one()->getResult();
        $this->assertEquals($id, $result['id']);

        $result = User::query()->where('id', $id)->one()->getResult();
        $this->assertEquals($id, $result->getId());
    }
}
