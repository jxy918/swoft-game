<?php declare(strict_types=1);


namespace SwoftTest\Db\Unit\Query;

use DateTime;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder;
use Swoft\Db\Query\Expression;
use Swoft\Stdlib\Collection;
use SwoftTest\Db\Testing\Entity\User;
use SwoftTest\Db\Unit\TestCase;

/**
 * Class BuilderTest
 *
 * @since 2.0
 */
class BuilderTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelect()
    {
        $expectSql = 'select `id`, `name` from `user`';

        DB::query()->from('xxx');
        $sql  = DB::table('user')->select(...['id', 'name'])->toSql();
        $sql2 = DB::table('user')->select('id', 'name')->toSql();

        $this->assertEquals($expectSql, $sql);
        $this->assertEquals($expectSql, $sql2);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectSub()
    {
        $expectSql = 'select (select `id` from `count`) as `c` from `user`';

        $subSql = 'select `id` from `count`';
        $strSql = DB::table('user')->selectSub($subSql, 'c')->toSql();

        $subCb = function (Builder $query) {
            return $query->select('id')->from('count');
        };
        $db    = DB::table('user');

        $builder = Builder::new()->from('count')->select('id');
        $cbSql   = $db->selectSub($subCb, 'c')->toSql();

        $buildSql = DB::table('user')->selectSub($builder, 'c')->toSql();


        $this->assertEquals($expectSql, $strSql);
        $this->assertEquals($expectSql, $cbSql);
        $this->assertEquals($expectSql, $buildSql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectRaw()
    {
        $expectSql = 'select (select `id` from `count` where c=?) as c from `user`';
        $sql       = DB::table('user')->selectRaw('(select `id` from `count` where c=?) as c',
            [1])->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectBetween()
    {
        $expectSql = 'select `age` from `user` where `age` between ? and ?';
        $db        = DB::table('user')->select('age')->whereBetween('age', [18, 25]);
        $sql       = $db->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectExpression()
    {
        $expectSql = 'select * from `user` where (`age` > 18 and age < 35) or `age` < 18';
        $sql       = DB::table('user')
            ->where(function (Builder $builder) {
                $builder->where('age', '>', Expression::new('18 and age < 35'));
            })
            ->orWhere('age', '<', Expression::new('18'))
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectLeftJoin()
    {
        $expectSql = 'select * from `count` left join `user` as `u` on `u`.`id` = `count`.`user_id`';
        $sql       = DB::table('count')
            ->join('user as u', 'u.id', '=', "count.user_id", 'left')
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectSubJoinQuery()
    {
        $expectSql = 'select * from `count` inner join (' .
            'select `age` from `user` where `age` between ? and ?' .
            ') as `user` on `user`.`id` = `count`.`user_id` where `id` < ?';

        $connection = Builder::new();
        // Example1
        $user = $connection->newQuery()->from('user')->select('age')->whereBetween('age', [18, 25]);
        $sql  = $connection->newQuery()->from('count')
            ->where('id', '<', 200)
            ->joinSub($user, 'user', 'user.id', '=', "count.user_id")
            ->toSql();

        // Example2
        $user1 = 'select `age` from `user` where `age` between ? and ?';
        $sql1  = $connection->newQuery()->from('count')
            ->where('id', '<', 200)
            ->joinSub($user1, 'user', 'user.id', '=', "count.user_id")
            ->toSql();

        $this->assertEquals($expectSql, $sql1);
        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectList()
    {
        $expectSql = 'select * from `user` where `name` like ? and `id` > ? and (`age` = ?) ' .
            'or `user_desc` between ? and ? ' .
            'and (exists (select `user_id` from `count` where `user_id` between ? and ?))';
        $where     = [
            'age' => 18
        ];
        $sql       = DB::table('user')
            ->where('name', "like", "lit%")
            ->where('id', '>', 1000)
            ->where($where)
            ->whereBetween('user_desc', [1, 5], 'or', false)
            ->where(function (Builder $builder) {
                $count = $builder
                    ->newQuery()
                    ->from('count')
                    ->select('user_id')
                    ->whereBetween('user_id', [1, 5]);

                $builder->addWhereExistsQuery($count);
            })
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testHaving()
    {
        $expectSql = 'select `sum(age)` as `sum_age`, `count(user_desc)` as `count_desc`, `id` from `user` ' .
            'group by `user_desc` ' .
            'having `count_desc` = ? or `sum_age` > ?';


        $sql1 = DB::table('user')
            ->select('sum(age) as sum_age')
            ->addSelect(['count(user_desc) as count_desc', 'id'])
            ->groupBy('user_desc')
            ->having('count_desc', "1")
            ->having('sum_age', '>', "1", 'or')
            ->toSql();
        $sql2 = DB::table('user')
            ->select('sum(age) as sum_age')
            ->addSelect(['count(user_desc) as count_desc', 'id'])
            ->groupBy('user_desc')
            ->having('count_desc', "1")
            ->orHaving('sum_age', '>', "1")
            ->toSql();
        $sql3 = DB::table('user')
            ->select('sum(age) as sum_age')
            ->addSelect(['count(user_desc) as count_desc', 'id'])
            ->groupBy('user_desc')
            ->having('count_desc', "1")
            ->havingRaw("`sum_age` > ?", ["1"], 'or')
            ->toSql();

        $this->assertEquals($expectSql, $sql1);
        $this->assertEquals($expectSql, $sql2);
        $this->assertEquals($expectSql, $sql3);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testDistinct()
    {
        $expectSql = 'select distinct * from `user`';
        $sql       = DB::table('user')->distinct()->toSql();
        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testWheres()
    {
        $expectSql = 'select `user`.* from `user` ' .
            'inner join `count` as `c` on `user`.`id` = ? ' .
            'where exists (select * where `age` > ?) ' .
            'and date(`create_time`) > ? ' .
            'and year(`birthday`) = ? ' .
            'and month(`update_time`) = ? ' .
            'and day(`now`) < ? ' .
            'and `c`.`id` in (1, 4, 5) ' .
            'and json_contains(`user_info`, ?) ' .
            'and json_length(`shop_label`) = ? ' .
            'and (`user_account` is not null ' .
            'and time(`user_time`) = ? ' .
            'and (`a`, `d`) = (?, ?))';;
        $sql = DB::table('user')
            ->select('user.*')
            ->whereExists(function (Builder $builder) {
                return $builder->where('age', '>', 10);
            })
            ->joinWhere('count as c', 'user.id', '=', 'c.user_id')
            ->whereDate('create_time', '>', date('y-m-d'))
            ->whereYear('birthday', '=', date('Y'))
            ->whereMonth('update_time', date('m'))
            ->whereDay('now', '<', date('d'))
            ->whereIntegerInRaw('c.id', [1, 4, 5])
            // Search json field user_info[1]
            ->whereJsonContains('user_info', '1')
            ->whereJsonLength('shop_label', '0')
            // Add a nested where statement to the query.
            ->whereNested(function (Builder $builder) {
                $builder->whereNotNull('user_account');

                $builder->whereTime('user_time', time())
                    // Result (`a`, `d`) = (?, ?))
                    ->whereRowValues(['a', 'd'], '=', [1, 3]);
            })
            ->toSql();
        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testOrWheres()
    {
        $expectSql = 'select * from `user` ' .
            'where `user_desc` between ? and ? ' .
            'or exists (select * from (select * from `user`) as `user`) ' .
            'or not exists (select * from count order by `id` desc, `name` asc) ' .
            'or date(`create_time`) > ? ' .
            'or json_contains(`user_info`, ?) ' .
            'or json_length(`shop_label`) = ? ' .
            'or day(`now`) < ? ' .
            'or `user_info` is not null ' .
            'or `id` not in (?, ?, ?) ' .
            'or year(`birthday`) = ? ' .
            'or `user_desc` not between ? and ? ' .
            'or st_distance(point(`lng`, `lat`), point(?, ?) ) * 111195 as distance ' .
            'having `distance` < ? ' .
            'order by `distance` desc';
        $sql       = DB::table('user')
            //  = whereBetween('user_desc', [1, 5], 'or' )
            ->orWhereBetween('user_desc', [1, 5])
            //  = whereExists($callback, 'or' )
            ->orWhereExists(function (Builder $builder) {
                $newQuery = $builder->newQuery()->from('user');
                return $builder->fromSub($newQuery, 'user');
            })
            ->orWhereNotExists(function (Builder $builder) {
                return $builder->fromRaw('count')->orderByDesc('id')->orderByRaw('`name` asc');
            })
            //  = whereDate('create_time', '>', date('d'), 'or') ....
            ->orWhereDate('create_time', '>', date('y-m-d'))
            ->orWhereJsonContains('user_info', '1')
            ->orWhereJsonLength('shop_label', '0')
            ->orWhereDay('now', '<', new DateTime('2013-03-29T04:13:35-0600'))
            ->orWhereNotNull('user_info')
            ->orWhereNotIn('id', [1, 4, 6])
            ->orWhereYear('birthday', '=', '1996')
            ->orWhereNotBetween('user_desc', [2, 6])
            ->orWhereRaw('st_distance(point(`lng`, `lat`), point(?, ?) ) * 111195 as distance', [117.069, 35.86])
            ->having('distance', '<', '50')
            ->orderBy('distance', 'desc')
            ->toSql();
        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectShareLock()
    {
        $expectSql = 'select * from `user` lock in share mode';
        $sql       = DB::table('user')
            ->sharedLock()
            ->toSql();;
        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testSelectWriteLock()
    {
        $expectSql = 'select * from `user` for update';
        $sql       = DB::table('user')
            ->lock()
            ->toSql();

        $sql1 = DB::table('user')
            ->lockForUpdate()
            ->where(function () {

            })
            ->toSql();

        $sql2 = DB::raw($expectSql);

        $this->assertEquals($expectSql, $sql);
        $this->assertEquals($expectSql, $sql1);
        $this->assertEquals($expectSql, $sql2);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testForceIndex()
    {
        $expectSql = 'select `id`, `name` from `user` force index(`idx_user`)';

        $sql = DB::table('')
            ->select('id', 'name')
            ->fromRaw('`user` force index(`idx_user`)')
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testInset()
    {
        $raw = DB::insert(
            'INSERT INTO `user` ( `name`, `age`, `password`, `user_desc`)VALUES( ?, ?, ?,?);',
            ["sakura", "23", "34asdfasdf", "XX"]
        );
        $this->assertTrue($raw);

        $insertUpdate = DB::table('user')->updateOrInsert(
            [
                'id' => 22,
            ],
            [
                'name' => 'sakuraovq' . mt_rand(1, 100)
            ]
        );
        $first        = $this->getFirstId();
        $inc          = DB::table('user')->where('id', $first)->increment('age', 3);
        $dec          = DB::table('user')->where('id', $first)->decrement('age', 2);

        $insert = DB::table('user')->insert([
            'name'     => 'sakuraovq' . mt_rand(1, 100),
            'password' => md5((string)mt_rand(1, 100)),
        ]);

        $insert = DB::table('user')->insert([
            [
                'name'     => 'sakuraovq' . mt_rand(1, 100),
                'password' => md5((string)mt_rand(1, 100)),
            ],
            [
                'name'     => 'sakuraovq' . mt_rand(1, 100),
                'password' => md5((string)mt_rand(1, 100)),
            ],
            [
                'name'     => 'sakuraovq' . mt_rand(1, 100),
                'password' => md5((string)mt_rand(1, 100)),
            ],
            [
                'name'     => 'sakuraovq' . mt_rand(1, 100),
                'password' => md5((string)mt_rand(1, 100)),
            ]
        ]);

        // sync subQuery data to table
        $using = Builder::new()->from('user')->insertUsing(['name', 'age'], function (Builder $builder) {
            return $builder->from('user')
                ->select('name', 'age')
                // close sync
                ->where('id', 0);
        });
        // get insert id
        $id = DB::table('user')->insertGetId([
            'age'       => 18,
            'name'      => 'Sakura',
            'test_json' => json_encode([
                'age'  => 18,
                'name' => 'Sakura',
            ]),
        ]);

        $this->assertIsString($id);
        $this->assertTrue($using);
        $this->assertTrue($insert);
        $this->assertTrue($insertUpdate);
        $this->assertIsInt($inc);
        $this->assertIsInt($dec);
        $this->assertIsInt($first);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testUpdate()
    {
        $this->assertTrue(DB::table('user')->updateOrInsert(['id' => 1]));
        $this->assertTrue(DB::table('user')->updateOrInsert(['id' => 1]));
        $this->assertTrue(DB::table('user')->updateOrInsert(['id' => 2], ['age' => 96]));
        $this->assertTrue(DB::table('user')->updateOrInsert(['id' => 2]));

        $id     = $this->getFirstId();
        $update = DB::table('user')->where('id', $id)->update(['age' => 18, 'name' => 'ovo' . mt_rand(22, 33)]);
        // random update
        $update1 = DB::table('user')->inRandomOrder()->limit(1)->update([
            'age'  => mt_rand(22, 33),
            'name' => 'rd' . substr(uniqid(), mt_rand(3, 10))
        ]);
        $delete  = DB::table('user')->delete($id);
        $doesNot = DB::table('user')->where('id', $id)->doesntExist();

        $res = DB::update('update `user` set `age` = ? where `id` = ?', [mt_rand(2, 55), $this->getFirstId()]);

        $this->assertEquals($res, 1);
        $this->assertTrue($doesNot);
        $this->assertEquals($delete, 1);
        $this->assertEquals($update1, 1);
        $this->assertIsInt($update);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testDelete()
    {
        $id         = mt_rand(2333333, 223333333333);
        $deleteNull = DB::table('user')->delete($id);

        $deleteRange = Builder::new()
            ->from('user')
            ->whereBetween('age', [0, 18])
            ->delete();

        $deleteRegexp = DB::table('user')
            ->where('name', 'regexp', '[a-z]{' . mt_rand(6, 9) . ',20}')
            ->delete();

        $orderByDel   = DB::table('user')
            ->limit(1)
            ->orderBy('id')
            ->delete();
        $deleteMethod = DB::delete('delete from `user` where id = ?', [$this->getFirstId()]);

        $this->assertEquals($deleteMethod, 1);
        $this->assertIsInt($deleteRange);
        $this->assertIsInt($orderByDel);
        $this->assertIsInt($deleteRegexp);
        $this->assertEquals($deleteNull, 0);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testCrossJoin()
    {
        $expectSql = 'select * from `user` cross join `count` on `count`.`user_id` = `user`.`id`';
        $sql       = Builder::new()
            ->from('user')
            ->crossJoin('count', 'count.user_id', '=', 'user.id')
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testUnionSelect()
    {
        $expectSql = '(select * from `user`) union all (select * from `user`) union (select * from `user`)';
        $sql       = Builder::new()
            ->from('user')
            ->unionAll(function (Builder $builder) {
                $builder->from('user');
            })
            ->union(Builder::new()->from('user'))
            ->toSql();

        $this->assertEquals($expectSql, $sql);
    }

    /**
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function testEach()
    {
        // You must specify an orderBy clause when using this function.
        // Traverse through all data and take 4 pieces at a time
        $res = DB::table('user')->orderBy('id')->each(function ($result) {
            // use data to do something...
            if ($result) {
                // return false once .. break 'each' traverse
                return false;
            }
            return true;
        }, 4);

        $this->assertIsBool($res);
    }

    public function testDBClass()
    {
        $expectSql = 'select * from `user` where `id` = ?';

        $sql = Builder::new()->from('user')->where('id', '0')->toSql();
        $this->assertEquals($expectSql, $sql);
        // flush db
        DB::table('user')->truncate();
        // Single strip
        $res = DB::selectOne($sql, [$this->getFirstId()]);
        $this->assertIsInt($res['id']);


        // Multiple strips
        $select = DB::select($expectSql, [$this->getFirstId()]);
        $this->assertIsArray($select);

        // NOTE:: DDL , return bool
        $unprepared = DB::unprepared('DROP TRIGGER IF EXISTS `sync_to_item_table`');
        $this->assertIsBool($unprepared);

        $sql = 'select * from `user`';
        $res = DB::cursor($sql);
        foreach ($res as $user) {
            $this->assertIsString($user['name']);
        }
        //DB::statement('drop table `count`');
    }

    /**
     * get first line `id`
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getFirstId(): int
    {
        $res = DB::table('user')->first(['id']);
        if (empty($res)) {
            return User::updateOrCreate(['id' => 22], ['age' => 1, 'name' => 'sakuraovq'])->getId();
        }
        return $res['id'];
    }

    public function testPool()
    {
        $id  = $this->getFirstId();
        $res = DB::query('db.pool2')->from('user')->where('id', $id)->get();

        foreach ($res as $v) {
            $this->assertInstanceOf('stdClass', $v);
        }
        $this->assertIsArray($res->toArray());
    }

    public function testGetValue()
    {
        $id = DB::table('user')->from('user')->where('id', $this->getFirstId())->value('id');
        $this->assertGreaterThan(0, $id);
    }

    public function testChunk()
    {
        $this->getFirstId();
        DB::table('user')->orderBy('id')->chunk(100, function (Collection $users) {
            $this->assertIsArray($users->toArray());
            return false;
        });

        $users = DB::table('user')->cursor();
        foreach ($users as $user) {
            $this->assertIsString($user['name']);
        }
    }

    public function testSelectChinese()
    {
        $name = '哈哈哈哈哈' . mt_rand(1, 1000);

        $user   = User::updateOrCreate(['id' => 22], ['age' => 18, 'name' => $name]);
        $result = (object)DB::table('user')->find((string)$user->getId());

        $this->assertEquals($name, $result->name);


        DB::table('user')->where('id', $user->getId())->update(['name' => $name]);
        $result1 = (object)DB::table('user')->find((string)$user->getId());

        $this->assertEquals($name, $result1->name);
    }

    public function testExpUpdate()
    {
        $id  = DB::table('user')->insertGetId(
            ['name' => 'dayle@example.com111', 'age' => 0]
        );
        $res = DB::table('user')->where('id', $id)->update(
            [
                'age'  => Expression::new('age + 1'),
                'name' => 'updated',
            ]
        );
        $this->assertEquals(true, $res);
    }

    public function testCollection()
    {
        $collection = new Collection([
            ['a' => 1, 'b' => 1],
            ['a' => 1, 'b' => 1],
            ['a' => 1, 'b' => 31],
            ['a' => 5, 'b' => 21]
        ]);

        $this->assertArrayHasKey(1, $collection->groupBy(['a', 'b']));
        $this->assertArrayHasKey(5, $collection->groupBy(['a', 'b']));
    }

    public function testDbPool()
    {
        $name = "swoft \ntest";
        $isOK = DB::query('db.pool2')->from('user')->updateOrInsert(['id' => 22], ['name' => $name]);
        $this->assertTrue($isOK);

        $res = DB::query('db.pool2')->from('user')->find("22");
        $this->assertEquals($name, $res->name);
    }

    public function testPaginate()
    {
        $perPage = 2;
        $page    = 1;
        $res     = DB::table('user')->paginate($page, $perPage, ['name', 'password', 'id']);
        $res1    = DB::table('user')
            ->select('id')
            ->addSelect(['name', 'password'])
            ->where('id', '>', 0)
            ->paginate($page, $perPage);
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

    public function testChunkById()
    {
        $this->addRecord();
        DB::table('user')->chunkById(2, function ($users) {
            foreach ($users as $user) {
                $this->assertIsString($user['name']);
            }
        });
    }

    public function testWhereCallback()
    {
        $expectSql = 'select * from `user` where (`id` = ?) order by `id` desc';

        $res = DB::table('user')
            ->where(function (Builder $query) {
                // wrong writing
                $query->forPage(1, 10)
                    ->orderBy('age', 'ase')
                    ->where('id', 1);
            })
            ->orderBy('id', 'desc')
            ->toSql();

        $this->assertEquals($expectSql, $res);
    }

    public function testWhereArray()
    {
        $expectSql = 'select * from `user` where (`uid` = ? and `name` like ? or `phone` like ?)';

        $where = [
            'uid' => 1,
            ['name', 'like', '%xx%'],
            ['phone', 'like', 'xx%', 'or']
        ];

        $res = DB::table('user')->where($where)->toSql();

        $this->assertEquals($expectSql, $res);
    }
}
