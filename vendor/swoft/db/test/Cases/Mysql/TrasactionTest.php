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

use Swoft\Db\Db;
use SwoftTest\Db\Cases\AbstractMysqlCase;
use SwoftTest\Db\Testing\Entity\User;

class TrasactionTest extends AbstractMysqlCase
{
    public function testCommit()
    {
        $data = [
            'name' => 'name',
            'sex'  => 1,
            'desc' => 'desc2',
            'age'  => 100,
        ];

        Db::beginTransaction();

        $user = new User();
        $user->fill($data);
        $id = $user->save()->getResult();

        $user2 = new User();
        $user2->fill($data);
        $id2 = $user2->save()->getResult();
        Db::commit();


        $fid = User::findById($id)->getResult()['id'];
        $fid2 = User::findById($id2)->getResult()['id'];

        $this->assertEquals($id, $fid);
        $this->assertEquals($id2, $fid2);
    }

    public function testCommitByCo()
    {
        go(function () {
            $this->testCommit();
        });
    }

    public function testRollback()
    {
        $data = [
            'name' => 'name',
            'sex'  => 1,
            'desc' => 'desc2',
            'age'  => 100,
        ];

        Db::beginTransaction();

        $user = new User();
        $user->fill($data);
        $id = $user->save()->getResult();

        $user2 = new User();
        $user2->fill($data);
        $id2 = $user2->save()->getResult();
        Db::rollback();


        $fid = User::findById($id)->getResult();
        $fid2 = User::findById($id2)->getResult();

        $this->assertTrue(empty($fid));
        $this->assertTrue(empty($fid2));
    }

    public function testRollbackByCo()
    {
        go(function () {
            $this->testRollback();
        });
    }
}
