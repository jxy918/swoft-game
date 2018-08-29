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
use SwoftTest\Db\Testing\Entity\User;
use SwoftTest\Db\Cases\AbstractMysqlCase;

/**
 * SqlMysqlTest
 */
class SqlTest extends AbstractMysqlCase
{
    public function testInsert()
    {
        $name   = 'swoft insert';
        $result = Db::query('insert into user(name, sex,description, age) values("' . $name . '", 1, "xxxx", 99)')->getResult();
        $user   = User::findById($result)->getResult();

        $this->assertEquals($user['name'], $name);

        $result = Db::query('INSERT into user(name, sex,description, age) values("' . $name . '", 1, "xxxx", 99)')->getResult();
        $user   = User::findById($result)->getResult();
        $this->assertEquals($user['name'], $name);
    }

    public function testInsertByCo()
    {
        go(function () {
            $this->testInsert();
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testSelect($id)
    {
        $result = Db::query('select * from user where id=' . $id)->getResult();
        $this->assertCount(1, $result);

        $result = Db::query('SELECT * from user where id=' . $id)->getResult();
        $this->assertCount(1, $result);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testSelectByCo($id)
    {
        go(function () use ($id) {
            $this->testSelect($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testSelect2($id)
    {
        $result = Db::query('select * from user where id=:id and name=:name', ['id' => $id, ':name'=>'name'])->getResult();
        $result2 = Db::query('select * from user where id=? and name=?', [$id, 'name'])->getResult();
        $this->assertEquals($id, $result[0]['id']);
        $this->assertEquals($id, $result2[0]['id']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testSelect2ByCo($id)
    {
        go(function () use ($id) {
            $this->testSelect2($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testDelete($id)
    {
        $result = Db::query('delete from user where id=' . $id)->getResult();
        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testDeleteByCo($id)
    {
        go(function () use ($id) {
            $this->testDelete($id);
        });
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testUpdate($id)
    {
        $name   = 'update name1';
        $result = Db::query('update user set name="' . $name . '" where id=' . $id)->getResult();
        $this->assertEquals(1, $result);

        $name   = 'update name 协程框架';
        $result = Db::query('UPDATE user set name="' . $name . '" where id=' . $id)->getResult();
        $this->assertEquals(1, $result);

        $user = User::findById($id)->getResult();
        $this->assertEquals($name, $user['name']);
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param $id
     */
    public function testUpdateByCo($id)
    {
        go(function () use ($id) {
            $this->testUpdate($id);
        });
    }
}
