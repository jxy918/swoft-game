<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Db\Cases;

use SwoftTest\Db\Testing\Entity\User;

/**
 */
class EntityTest extends AbstractMysqlCase
{
    public function testToArray()
    {
        $age  = mt_rand(1, 100);
        $user = new User();
        $user->setId(12);
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge($age);

        $array = $user->toArray();

        $data = [
            'id'   => 12,
            'name' => 'name',
            'sex'  => 1,
            'desc' => 'this my desc',
            'age'  => $age,
        ];
        $this->assertEquals($data, $array);
    }

    public function testToJson()
    {
        $age  = mt_rand(1, 100);
        $user = new User();
        $user->setId(12);
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge($age);

        $json   = $user->toJson();
        $string = $user->__toString();
        $data   = '{"id":12,"name":"name","age":' . $age . ',"sex":1,"desc":"this my desc"}';
        $this->assertEquals($data, $json);
        $this->assertEquals($data, $string);
    }

    public function testArrayAccess()
    {
        $age  = mt_rand(1, 100);
        $user = new User();
        $user->setId(12);
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');

        $user['age'] = $age;

        $this->assertEquals('name', $user['name']);
        $this->assertEquals($age, $user['age']);
        $this->assertTrue(isset($user['sex']));
    }

    /**
     * @dataProvider mysqlProvider
     *
     * @param int $id
     */
    public function testIterator($id)
    {
        $user = User::findById($id)->getResult();
        $data = [];
        foreach ($user as $key => $value) {
            $data[$key] = $value;
        }

        $this->assertEquals($data, $user->toArray());
    }

    public function testArrayAttr()
    {
        $data = [
            'name' => 'name',
            'sex'  => 1,
            'desc' => 'desc2',
            'age'  => 100,
        ];

        $user   = new User();
        $result = $user->fill($data)->save()->getResult();

        $resultUser = User::findById($result)->getResult();
        $this->assertEquals('name', $resultUser['name']);
        $this->assertEquals(1, $resultUser['sex']);
        $this->assertEquals('desc2', $resultUser['desc']);
        $this->assertEquals(100, $resultUser['age']);


        $user2         = new User();
        $user2['name'] = 'name2';
        $user2['sex']  = 1;
        $user2['desc'] = 'this my desc9';
        $user2['age']  = 99;

        $result2     = $user2->save()->getResult();
        $resultUser2 = User::findById($result2)->getResult();

        $this->assertEquals('name2', $resultUser2['name']);
        $this->assertEquals(1, $resultUser2['sex']);
        $this->assertEquals('this my desc9', $resultUser2['desc']);
        $this->assertEquals(99, $resultUser2['age']);
    }
}
