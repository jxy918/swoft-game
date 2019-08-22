<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit\Eloquent;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\DB;
use Swoft\Db\Eloquent\Collection;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Expression;
use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\User;
use SwoftTest\Db\Unit\TestCase;

/**
 * Class ModelTest
 *
 * @since 2.0
 */
class ModelTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSave()
    {
        $user = new User;
        $user->setAge(mt_rand(1, 100));
        $user->setUserDesc('desc');

        // Save result
        $result = $user->save();
        $this->assertTrue($result);

        // Insert id

        $this->assertTrue($user->getId() >= 1);

        $user2 = User::new();
        $user2->setAge(100);

        // Save result
        $result2 = $user2->save();
        $this->assertTrue($result2);

        // Properties
        $attributes = [
            'name'      => uniqid(),
            'password'  => md5(uniqid()),
            'age'       => mt_rand(1, 100),
            'user_desc' => 'u desc'
        ];
        $result3    = User::new($attributes)->save();
        $this->assertTrue($result3);

        $batch = User::insert([
            [
                'name'      => uniqid(),
                'password'  => md5(uniqid()),
                'age'       => mt_rand(1, 100),
                'user_desc' => 'u desc',
                'foo'       => 'bar'
            ],
            [
                'name'      => uniqid(),
                'password'  => md5(uniqid()),
                'age'       => mt_rand(1, 100),
                'user_desc' => 'u desc',
                'xxxx'      => '223asdf'
            ]
        ]);

        Count::insert([
            [
                'user_id'     => 1,
                'attributes'  => uniqid(),
                'create_time' => 111,
            ],
            [
                'user_id'     => 2,
                'attributes'  => uniqid(),
                'create_time' => 222,

            ],
        ]);
        $this->assertTrue($batch);


        $getId = User::insertGetId([
            'name'      => uniqid(),
            'password'  => md5(uniqid()),
            'age'       => mt_rand(1, 100),
            'user_desc' => 'u desc',
            'foo'       => 'bar',
            'xxxx'      => '223asdf'
        ]);
        $this->assertGreaterThan(0, $getId);

        $isOK = User::updateOrInsert(['id' => 22], [
            'name'      => uniqid(),
            'password'  => md5(uniqid()),
            'age'       => mt_rand(1, 100),
            'user_desc' => 'u desc',
            'foo'       => 'bar',
            'xxxx'      => '223asdf'
        ]);
        $this->assertTrue($isOK);

        $user = User::create($attributes);
        $this->assertIsObject($user);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testDelete()
    {
        $id   = $this->addRecord();
        $user = User::find($id);

        $result = $user->delete();
        $this->assertTrue($result);
    }

    public function testUpdateByWhere()
    {
        $res1 = User::updateOrCreate(['id' => 1], ['age' => 18, 'name' => 'sakuraovq']);


        $res2     = User::updateOrInsert(['id' => mt_rand(1, 20)], ['age' => 18, 'name' => 'sakuraovq']);
        $wheres   = [
            'name' => 'sakuraovq',
            ['id', '>=', 2]
        ];
        $orWheres = [
            ['name', 'like', '%s%']
        ];
        $result   = User::where($wheres)->orWhere($orWheres)->update(['name' => 'sakuraovq' . mt_rand(1, 10)]);
        $this->assertGreaterThan(0, $result);

        $updateBeforeAge = $res1->getAge();
        // update by id
        $updateByWhereId = User::where('id', 1)->increment('age', 1);
        $updateByModel   = User::find(1)->decrement('age', 2);
        $updateByModel2  = User::find(1)->increment('age', 1);

        $this->assertEquals(1, $updateByWhereId);
        $this->assertEquals(1, $updateByModel);
        $this->assertEquals(1, $updateByModel2);

        /* @var User $updateAfter */
        $updateAfter = User::find(1);
        $this->assertEquals($updateBeforeAge, $updateAfter->getAge());
    }

    public function testBatchDelete()
    {
        /* @var User $res1 */
        $res1 = User::updateOrCreate(['id' => 1], ['age' => 18]);

        $res2 = User::updateOrCreate(['id' => 2], ['age' => 18]);

        $result = User::whereIn('id', [$res1->getId(), $res2->getId()])->delete();
        $this->assertEquals(2, $result);


        /* @var User $res3 */
        $res3 = User::updateOrCreate(['id' => 5], ['age' => 18, 'name' => 'sakura']);

        $wheres    = [
            'age' => 18,
            ['id', '>=', 2]
        ];
        $orWheres  = [
            ['name', 'like', '%s%']
        ];
        $expectSql = 'select * from `user` where (`age` = ? and `id` >= ?) or (`name` like ?)';
        $this->assertEquals($expectSql, User::where($wheres)->orWhere($orWheres)->toSql());

        $resultDelete = User::where($wheres)->orWhere($orWheres)->delete();

        $this->assertGreaterThan(0, $resultDelete);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testUpdate()
    {
        $id   = $this->addRecord();
        $user = User::find($id);

        $name   = uniqid();
        $result = $user->update(['name' => $name]);
        $this->assertTrue($result);

        /* @var User $user2 */
        $user2 = User::find($id);
        $this->assertEquals($name, $user2->getName());

        // Update
        $aName = uniqid();
        $aAge  = mt_rand(1, 100);

        /* @var User $aUser */
        $aUser = User::updateOrCreate(['name' => $aName], ['age' => $aAge]);

        $this->assertEquals($aUser->getName(), $aName);
        $this->assertEquals($aUser->getAge(), $aAge);

        // Create
        $uName = uniqid();
        $uAge  = mt_rand(1, 100);

        /* @var User $uUser */
        $uUser = User::updateOrCreate(['id' => $id],
            ['name' => $uName, 'age' => $uAge]);

        /* @var User $user */
        $user = User::find($id);
        $this->assertEquals($uName, $user->getName());
        $this->assertEquals($uAge, $user->getAge());

        $uiName = uniqid();
        $result = User::updateOrInsert(['id' => $id], ['name' => $uiName]);

        $this->assertTrue($result);

        /* @var User $user */
        $user = User::find($id);
        $this->assertEquals($uiName, $user->getName());
    }

    public function testModelSelect()
    {
        $uUser = User::updateOrCreate(['id' => 22], ['name' => "sakura", 'age' => 18]);

        $user = User::find(22);
        $user->addHidden(['age']);
        $user->setModelVisible(['password']);
        $user->addHidden(['password']);
        $user->addVisible(['age']);
        $user->addVisible(['pwd']);

        DB::transaction(function () {

        });
        $this->assertArrayHasKey('pwd', $user->toArray());
        // Delete only left 20 rows
        $resCount
            = DB::selectOne('select count(*) as `count` from `user`')['count'];
        if ($resCount - 20 > 0) {
            DB::delete('delete A FROM `user` A INNER JOIN (SELECT ID FROM `user` B limit ?) B
on A.id=B.id;', [$resCount - 20]);
            $res
                = DB::selectOne('select count(*) as `count` from `user`')['count'];
            $this->assertEquals(20, $res);
        }
        foreach (User::query()->cursor() as $user) {
            /* @var User $user */
            $this->assertGreaterThan(0, $user->getId());
        }

        // query all, each 10 strips
        User::query()->chunk(10, function ($users) {
            foreach ($users as $user) {
                /* @var User $user */
                $this->assertIsInt($user->getId());
            }
        });
    }

    public function testPick()
    {
        $result   = User::pluck('name', 'age');
        $dbResult = DB::table('user')->pluck('name', 'age');

        $this->assertEquals($result, $dbResult);

        foreach ($result as $age => $name) {
            $this->assertIsInt($age);
            $this->assertIsString($name);
        }
    }

    public function testImplode()
    {
        $ageString = DB::table('user')
            ->where('age', '>', 18)
            ->implode('age', ',');

        $this->assertEquals($ageString,
            User::where('age', '>', 18)
                ->implode('age', ','));
    }

    public function testAggregate()
    {
        $result = DB::table('user')->max('age');;

        $this->assertTrue(is_float($result) || is_int($result));
        $this->assertEquals($result, User::query()->max('age'));

        $result1 = User::min('age');

        $this->assertTrue(is_float($result1) || is_int($result1));
        $this->assertEquals($result1, DB::table('user')->min('age'));

        $result2 = User::query()->average('age');
        $this->assertEquals($result2, DB::table('user')->average('age'));

        $result3 = User::query()->avg('age');

        $this->assertTrue(is_float($result3) || is_int($result3));
        $this->assertEquals($result3, DB::table('user')->avg('age'));
        $this->assertEquals($result2, $result3);

        $result4 = User::query()->count();
        $this->assertIsInt($result4);
        $this->assertEquals($result4, DB::table('user')->count());

        // sql = select max(`id`) as id from `user` group by user_desc
        $res = User::query()
            ->selectRaw('max(`id`) as max_id')
            ->groupBy('user_desc')
            ->get();

        foreach ($res as $v) {
            $this->assertGreaterThan(0, $v['max_id']);
        }
        $ages = array_column($res->toArray(), 'id');
        $this->assertIsArray($ages);
    }

    public function testSkip()
    {

        $users = User::skip(10)->take(5)->get();

        foreach ($users as $user) {
            /* @var User $user */
            $this->assertIsInt($user->getId());
        }
        $this->assertInstanceOf('Swoft\Db\Eloquent\Collection', $users);
    }

    public function testWheres()
    {
        $expectSql
            = 'select * from `user` where (`name` = ? and `status` >= ? or `money` > ?)';

        $wheres = [
            'name' => 'sakuraovq',
            ['status', '>=', 2],
            ['money', '>', 0, 'or']
        ];
        $sql    = User::where($wheres)->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    public function testValue()
    {
        $res = User::value('user_desc');

        $this->assertIsString($res);
    }

    public function testChunkId()
    {
        User::chunkById(2, function (Collection $users) {
            /* @var User $user */
            foreach ($users as $user) {
                $this->assertGreaterThan(0, $user->getId());
            }
            $res = $users->pluck('age', 'id')->toArray();
            $this->assertIsArray($res);
        });

    }

    public function testGetModels()
    {
        User::updateOrCreate(['id' => 22], ['name' => "sakura", 'age' => 18]);
        $users = User::where('id', 22)->getModels(['id', 'age']);
        /* @var User $user */
        foreach ($users as $user) {
            $age = $user->getAge();
            $this->assertIsInt($age);
        }
    }

    public function testKeyBy()
    {
        $users = User::forPage(1, 10)->get(['id', 'age'])->keyBy('id');

        /* @var User $user */
        foreach ($users as $id => $user) {
            $this->assertIsInt($user->getAge());
            $this->assertIsInt($id);
        }
    }

    public function testPaginate()
    {
        $perPage = 2;
        $page    = 1;
        $res     = User::paginate($page, $perPage, ['id', 'name', 'password', 'user_desc']);
        $res1    = User::select('id')
            ->where('id', '>', 0)
            ->addSelect(['name', 'password', 'user_desc'])
            ->paginate($page, $perPage);


        $afterResult = User::where('name', '!=', '')
            ->from('user as u')
            ->leftJoin('count as c', 'u.id', '=', 'c.user_id')
            ->paginateById($perPage, 1, ['name', 'password'], true, 'u.id');

        $afterResult1 = User::where('name', '!=', '')
            ->from('user as u')
            ->paginateById($perPage, 1, ['name', 'password', 'user_desc']);

        $this->assertEquals($res['perPage'], $afterResult1['perPage']);
        $this->assertEquals($res['perPage'], $afterResult['perPage']);

        $this->assertEquals($res, $res1);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('list', $res);
        $this->assertArrayHasKey('count', $res);
        $this->assertArrayHasKey('perPage', $res);
        $this->assertArrayHasKey('pageCount', $res);
        $this->assertArrayHasKey('page', $res);

        $this->assertEquals($res['page'], $page);
        $this->assertEquals($res['perPage'], $perPage);
    }

    public function testFull()
    {
        $expect     = "testHump,哈";
        $attributes = ['testHump' => $expect];

        $user = User::new($attributes);
        $this->assertEquals($expect, $user->getTestHump());

        $userArray = User::new()->fill($attributes)->toArray();
        $this->assertArrayHasKey('testHump', $userArray);
        $this->assertEquals($expect, $userArray['testHump']);
    }

    public function testJoin()
    {
        $this->addRecord();
        $userCounts = User::join('count', 'user.id', '=', 'count.user_id')->get();

        foreach ($userCounts as $user) {
            $this->assertIsArray($user);
            $this->assertArrayHasKey('user_id', $user);
            $this->assertArrayHasKey('create_time', $user);
            $this->assertArrayHasKey('name', $user);
        }
    }

    public function testSelectRaw()
    {
        $this->addCountRecord();

        $field = 'count(*)';
        $res   = (array)Count::selectRaw($field)->first();
        $this->assertArrayHasKey($field, $res);
    }

    public function testSetter()
    {

        $expectAge = 18;
        $expectId  = 1;

        $user = User::new(['age' => 17, 'id' => 2]);
        $user->setAge($expectAge);
        $user->setId($expectId);

        $result = $user->toArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('age', $result);
        $this->assertEquals($expectAge, $result['age']);
        $this->assertEquals($expectId, $result['id']);
    }

    public function testGet()
    {
        $res = User::get(['id']);

        foreach ($res as $user) {
            $user = $user->toArray();
            $this->assertTrue(count($user) == 1);
            $this->assertArrayHasKey('id', $user);
        }
    }

    public function testAndWheres()
    {
        $sql = 'select * from `user` where `id` = ? and `age` > ?';

        $res = User::where('id', 1)->where('age', '>', 18)->toSql();

        $this->assertEquals($sql, $res);
    }

    public function testDistinct()
    {
        $expect = 'select distinct age from `user`';
        $sql    = User::distinct()->selectRaw('age')->toSql();

        $this->assertEquals($expect, $sql);
    }

    public function testWhereArray()
    {
        $ids = [1, 2];

        $expectSql = 'select * from `user` where (`id` in (?, ?))';

        $where = ['id' => $ids];
        $sql   = User::where($where)->toSql();
        $this->assertEquals($expectSql, $sql);

        $expectSql1 = 'select * from `user` where `id` in (?, ?)';
        $sql1       = User::where('id', '=', $ids)->toSql();
        $this->assertEquals($expectSql1, $sql1);
    }

    public function testCollection()
    {
        $collection = \Swoft\Stdlib\Collection::make([1, 1, 2, 2, 3, 4, 2]);

        $unq = $collection->unique();

        $this->assertCount(4, $unq->all());
    }

    public function testBatchUpdate()
    {
        $json = ['aa' => "hahhh", "有点厉害鸭" . time()];
        $age  = mt_rand();

        $this->testAutoJson();
        $id  = $this->addRecord();
        $id2 = $this->addRecord();
        $id3 = $this->addRecord();

        $values = [
            ['id' => $id3, 'test_json' => $json, 'age' => $age],
            ['id' => $id2, 'age' => $age, 'test_json' => $json,],
            ['id' => $id, 'age' => $age, 'test_json' => $json,],
        ];
        $count  = User::batchUpdateByIds($values);

        $this->assertCount($count, $values);

        $users = User::findMany([$id, $id2, $id3]);

        $this->assertCount($count, $users);

        /* @var $user User */
        foreach ($users as $user) {
            $this->assertEquals($age, $user->getAge());
            $this->assertEquals($json, $user->getTestJson());
        }
    }

    public function testAutoJson()
    {
        $id     = 18036;
        $json   = ['aa' => "hahhh", "有点厉害鸭"];
        $result = User::updateOrCreate(['id' => $id], [
            'test_json' => null,
            'user_desc' => 'xxxxxx',
            'hahh'      => 0,
            'age'       => Expression::new('`age` + 1'),
        ]);
        $this->assertEquals(0, $result->getHahh());

        $id     = 18037;
        $result = User::updateOrInsert(['id' => $id], [
            'test_json' => $json,
            'age'       => Expression::new('`age` + 1'),
        ]);

        $this->assertTrue($result);

        $user = User::find($id);
        $this->assertEquals($json, $user->getTestJson());
    }

    public function testAutoTimestamp()
    {
        // create time
        $count = Count::new();
        $count->setUserId($this->addRecord());
        $count->save();
        $this->assertGreaterThan(0, $count->getCreateTime());
        $this->assertGreaterThan(0, strtotime($count->getUpdateTime()));


        $newCount = Count::find($count->getId());
        $divTime  = '2019-07-11 17:00:1';

        $newCount->setUpdateTime($divTime);
        // update time
        $result = $newCount->update(['user_id' => 12233]);

        $this->assertTrue($result);
        $this->assertEquals($divTime, $newCount->getUpdateTime());
        $this->assertGreaterThan(0, strtotime($newCount->getUpdateTime()));
    }

    public function testUpdateEntity()
    {
        $count = Count::new(['create_time' => 0]);
        $count->setAttributes("swoft");

        $this->assertTrue($count->save());
        $this->assertEquals('swoft', $count->getAttributes());
        $this->assertEquals(0, $count->getCreateTime());
        $this->assertEquals(time(), strtotime($count->getUpdateTime()));


        $time   = '2018-03-06 21:09:18';
        $result = $count->fill([
            'update_time' => $time,
            'user_id'     => Expression::new('`create_time` + 1'),
        ])->update();
        $this->assertTrue($result);
        $this->assertEquals($time, $count->getUpdateTime());
        $this->assertEquals($time, Count::find($count->getId())->getUpdateTime());
        $this->assertEquals(null, $count->getUserId());


        $expect = 'swoft-framework';
        $count1 = Count::find($count->getId());
        $count1->setAttributes($expect);
        $count1->save();

        $this->assertEquals($expect, $count1->getAttributes());
        $this->assertEquals($expect, Count::find($count->getId())->getAttributes());
    }

    public function testModify()
    {
        $id          = 18039;
        $expectLabel = 'CCP';

        User::updateOrCreate(['id' => $id], [
            'test_json' => [],
            'user_desc' => 'CP',
            'age'       => 1,
        ]);

        $row = User::modify(['user_desc' => 'CP'], ['user_desc' => $expectLabel]);
        $this->assertEquals(true, $row);
        $this->assertEquals($expectLabel, User::find($id)->getUserDesc());

        $row = User::modifyById($id, ['user_desc' => $expectLabel]);
        $this->assertEquals(true, $row);
    }

    public function testUpdateAllCounters()
    {
        $id          = 18038;
        $expectLabel = 'CCP';

        $user = User::updateOrCreate(['id' => $id], [
            'test_json' => [],
            'user_desc' => 'HH',
            'age'       => 1,
        ]);

        User::updateAllCountersById((array)$id, ['age' => 1], ['user_desc' => $expectLabel]);
        $this->assertEquals($user->getAge() + 1, User::find($id)->getAge());
        $this->assertEquals($expectLabel, User::find($id)->getUserDesc());

        User::updateAllCounters(['user_desc' => $expectLabel], ['age' => -1]);
        $this->assertEquals($user->getAge(), User::find($id)->getAge());

        User::updateAllCountersAdoptPrimary(['user_desc' => $expectLabel], ['age' => 1]);
        DB::table('user')->updateAllCountersAdoptPrimary(['user_desc' => $expectLabel], ['age' => -1]);
        $this->assertEquals($user->getAge(), User::find($id)->getAge());


        DB::table('user')->updateAllCounters(['user_desc' => $expectLabel], ['age' => -1]);
        $this->assertEquals($user->getAge() - 1, User::find($id)->getAge());

        User::find($id)->updateCounters(['age' => -1]);
        $this->assertEquals($user->getAge() - 2, User::find($id)->getAge());
    }

    public function testGetEmpty()
    {
        $emptyCollection = User::where('id', '<', 0)->get(['age']);
        $this->assertEquals([], $emptyCollection->toArray());

        $userCounts = User::where('user.id', '<', 0)
            ->join('count', 'user.id', '=', 'count.user_id')
            ->get(['user.id']);
        $this->assertEquals([], $userCounts->toArray());
    }

    public function testUpdateJson()
    {
        $id   = 18038;
        $user = User::updateOrCreate(['id' => $id], [
            'test_json' => [
                'user_status' => mt_rand(),
                //                'balance'     => 0,
                //                'updated_at'  => null
            ],
            'user_desc' => 'HH',
            'age'       => 1,
        ]);

        // Model
        $row = $user->update(['test_json->user_status' => 2]);
        $this->assertEquals(1, $row);
        $this->assertEquals(2, User::find($id)->getTestJson()['user_status']);

        // Db
        $data = ['test_json->user_status' => 3];
        DB::table('user')->where('id', $id)->update($data);

        $this->assertEquals(3, User::where($data)->first()->getTestJson()['user_status']);

        $this->assertEquals(3, User::whereJsonContains('test_json->user_status', 3)
                                   ->first()
                                   ->getTestJson()['user_status']);

        $this->assertEquals(3, User::whereJsonLength('test_json->user_status', 1)
                                   ->first()
                                   ->getTestJson()['user_status']);

        DB::update("update `user` set `test_json` = null where `id` = :id", [':id' => 18038]);

        $name = User::tableName();
        $this->assertEquals('user', $name);
    }
}
