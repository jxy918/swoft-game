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
use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\User;

/**
 * RelationTest
 */
class RelationTest extends AbstractMysqlCase
{
    /**
     * @dataProvider relationProider
     *
     * @param int $uid
     */
    public function testJoin(int $uid)
    {
        $data = Query::table(User::class)->leftJoin(Count::class, 'user.id=count.uid')->andWhere('id', $uid)
            ->orderBy('user.id', QueryBuilder::ORDER_BY_DESC)->one(['user.id', 'user.name','count.fans', 'count.follows'])->getResult();

        $data2 = Query::table(User::class, 'u')->leftJoin(Count::class, 'u.id=c.uid', 'c')->andWhere('id', $uid)
            ->orderBy('u.id', QueryBuilder::ORDER_BY_DESC)->one(['u.id'=> 'userid', 'u.name','c.fans', 'c.follows'])->getResult();

        $this->assertEquals($uid, $data['id']);
        $this->assertEquals($uid, $data2['userid']);
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
}
