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
use Swoft\Db\QueryBuilder;
use SwoftTest\Db\Cases\AbstractMysqlCase;
use SwoftTest\Db\Testing\Entity\User;
use SwoftTest\Db\Testing\Entity\User2;

/**
 * MysqlTest
 */
class ActiveRecordTest extends AbstractMysqlCase
{
    public function testSave()
    {
        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $id     = $user->save()->getResult();
        $reuslt = $id > 0;
        $this->assertTrue($reuslt);
    }

    public function testUpdateWhenNameIsNull()
    {
        $user = new User2();
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));

        $id     = $user->save()->getResult();
        $reuslt = $id > 0;
        $this->assertTrue($reuslt);

        $user->setName('xxx');
        $rows = $user->update()->getResult();
        $reuslt = $rows > 0;
        $this->assertTrue($reuslt);
    }

    public function testSaveByCo()
    {
        go(function () {
            $this->testSave();
        });
    }

    public function testBatchInsert()
    {
        $values = [
            [
                'name'        => 'name',
                'sex'         => 1,
                'description' => 'this my desc',
                'age'         => 99,
            ],
            [
                'name'        => 'name2',
                'sex'         => 1,
                'description' => 'this my desc2',
                'age'         => 100,
            ],
        ];

        $result = User::batchInsert($values)->getResult();
        $this->assertTrue($result > 0);
    }

    public function testBatchInsertByCo()
    {
        go(function () {
            $this->testBatchInsert();
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDelete(int $id)
    {
        /* @var User $user */
        $user   = User::findById($id)->getResult();
        $result = $user->delete()->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDeleteByCo(int $id)
    {
        go(function () use ($id) {
            $this->testDelete($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDeleteById(int $id)
    {
        $result = User::deleteById($id)->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testDeleteByIdByCo(int $id)
    {
        go(function () use ($id) {
            $this->testDeleteById($id);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testDeleteByIds(array $ids)
    {
        $result = User::deleteByIds($ids)->getResult();
        $this->assertEquals($result, 2);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testDeleteByIdsByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testDeleteByIds($ids);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testUpdate(int $id)
    {
        $newName = 'swoft framewrok';

        /* @var User $user */
        $user = User::findById($id)->getResult();
        $user->setName($newName);
        $user->setDesc('new desc');
        $updateResult = $user->update()->getResult();
        $this->assertGreaterThanOrEqual(1, $updateResult);

        /* @var User $newUser */
        $newUser = User::findById($id)->getResult();
        $this->assertEquals($newName, $newUser->getName());

        $userObj = User::findById($id)->getResult();
        $userObj->setName('update');

        $result = $userObj->update()->getResult();

        $userObj = User::findById($id)->getResult();
        $userObj->setName('update');

        $result2 = $userObj->update()->getResult();

        $this->assertEquals(1, $result);
        $this->assertEquals(0, $result2);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testUpdateByCo(int $id)
    {
        go(function () use ($id) {
            $this->testUpdate($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindById(int $id)
    {
        $user      = User::findById($id)->getResult();
        $userEmpty = User::findById(99999999999)->getResult();
        $user2     = User::findById($id, ['fields' => ['id']])->getResult();
        $this->assertEquals($id, $user['id']);
        $this->assertEquals($userEmpty, null);

        $this->assertEquals($id, $user2['id']);
        $this->assertEquals(null, $user2['name']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindByIdByCo(int $id)
    {
        go(function () use ($id) {
            $this->testFindById($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindByIdClass(int $id)
    {
        /* @var User $user */
        $user      = User::findById($id)->getResult();
        $userEmpty = User::findById(99999999999)->getResult();
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($userEmpty, null);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindByIdClassByCo(int $id)
    {
        go(function () use ($id) {
            $this->testFindByIdClass($id);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindByIds(array $ids)
    {
        $users     = User::findByIds($ids)->getResult();
        $userEmpty = User::findByIds([999999999999])->getResult();
        $users2    = User::findByIds($ids, ['fields' => ['id'], 'orderby' => ['id' => 'asc'], 'limit' => 2])->getResult();

        sort($ids);
        $resultIds = [];
        foreach ($users as $user) {
            $resultIds[] = $user['id'];
        }
        sort($resultIds);
        $this->assertEquals($resultIds, $ids);
        $this->assertEquals($userEmpty, []);

        $queryIds = [];
        foreach ($users2 as $user) {
            $queryIds[] = $user['id'];
            $this->assertEquals(null, $user['name']);
        }


        $this->assertEquals($ids, $queryIds);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindByIdsByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testFindByIds($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindByIdsByClass(array $ids)
    {
        $users     = User::findByIds($ids)->getResult();
        $userEmpty = User::findByIds([999999999999])->getResult();

        $resultIds = [];
        /* @var User $user */
        foreach ($users as $user) {
            $resultIds[] = $user->getId();
        }
        $this->assertEquals(sort($resultIds), sort($ids));
        $this->assertEquals($userEmpty, []);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindByIdsByClassByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testFindByIdsByClass($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testQuery(array $ids)
    {
        $result = User::query()->orderBy('id', QueryBuilder::ORDER_BY_DESC)->limit(2)->get()->getResult();
        $this->assertCount(2, $result);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testQueryByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testQuery($ids);
        });
    }

    public function testDeleteOne()
    {
        $user         = new User();
        $user['name'] = 'name2testDeleteOne';
        $user['sex']  = 1;
        $user['desc'] = 'this my desc9';
        $user['age']  = 99;

        $uid    = $user->save()->getResult();
        $result = User::deleteOne(['name' => 'name2testDeleteOne', 'age' => 99, 'id' => $uid])->getResult();
        $this->assertEquals($result, 1);
    }

    public function testDeleteOneByCo()
    {
        go(function () {
            $this->testDeleteOne();
        });
    }


    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testDeleteAll(array $ids)
    {
        $result = User::deleteAll(['name' => 'name', 'id' => $ids])->getResult();
        $this->assertEquals(2, $result);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testDeleteAllByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testDeleteAll($ids);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testUpdateOne(int $id)
    {
        $result = User::updateOne(['name' => 'testUpdateOne'], ['id' => $id])->getResult();
        $user   = User::findById($id)->getResult();
        $this->assertEquals(1, $result);
        $this->assertEquals($user['name'], 'testUpdateOne');
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testUpdateOneByCo(int $id)
    {
        go(function () use ($id) {
            $this->testUpdateOne($id);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testUpdateAll(array $ids)
    {
        $result = User::updateAll(['name' => 'testUpdateAll'], ['id' => $ids])->getResult();
        $count  = User::findAll(['name' => 'testUpdateAll', 'id' => $ids])->getResult();
        $this->assertEquals(2, $result);
        $this->assertCount(2, $count);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindOne(int $id)
    {
        $user  = User::findOne(['id' => $id, 'name' => 'name'], ['id' => 'desc', 'age' => 'desc'])->getResult();
        $user2 = User::findOne(['id' => $id], ['fields' => ['id', 'name']])->getResult();
        $this->assertEquals($id, $user['id']);
        $this->assertEquals($id, $user2['id']);
        $this->assertEquals(null, $user2['desc']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testFindOneByCo(int $id)
    {
        go(function () use ($id) {
            $this->testFindOne($id);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindAll(array $ids)
    {
        $options = [
            'orderby' => [
                'id' => 'desc',
            ],
            'limit'   => 2,
            'offset'  => 0,
            'fields'  => ['id', 'name'],
        ];
        /* @var \Swoft\Db\Collection $result */
        $result = User::findAll(['name' => 'name', 'id' => $ids], $options)->getResult();
        $this->assertCount(2, $result);
        $this->assertEquals(2, $result->count());

        $jsonAry = json_decode($result->toJson(), true);
        foreach ($jsonAry as $userAry) {
            $this->assertEquals($userAry['name'], 'name');
            $this->assertTrue(in_array($userAry['id'], $ids));
        }

        foreach ($result->toArray() as $userAry) {
            $this->assertEquals($userAry['name'], 'name');
            $this->assertTrue(in_array($userAry['id'], $ids));
        }

        $ids = [];
        /* @var User $user */
        foreach ($result as $key => $user) {
            $ids[$key] = $user->getId();
            $this->assertEquals($user->getName(), 'name');
            $this->assertEquals($user->getDesc(), null);
        }

        $this->assertTrue($ids[0] > $ids[1]);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testFindAllByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testFindAll($ids);
        });
    }

    public function testExist()
    {
        $user = new User();
        $id   = $user->fill(['name' => 'existTest'])->save()->getResult();
        $this->assertTrue(User::exist($id)->getResult());
        $this->assertFalse(User::exist('NotExistId')->getResult());
    }

    public function testExistByCo()
    {
        go(function () {
            $this->testExist();
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCount(array $ids)
    {
        $count = User::count('id', ['id' => $ids])->getResult();
        $this->assertEquals(2, $count);
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testCountByCo(array $ids)
    {
        go(function () use ($ids) {
            $this->testCount($ids);
        });
    }

    /**
     * @dataProvider mysqlProviders
     *
     * @param array $ids
     */
    public function testQueryCount(array $ids)
    {
        $count = Query::table(User::class)->condition(['id' => $ids])->count()->getResult();
        $this->assertEquals(2, $count);
    }

}