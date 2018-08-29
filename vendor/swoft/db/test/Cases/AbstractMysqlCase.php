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

use SwoftTest\Db\Testing\Entity\Count;
use SwoftTest\Db\Testing\Entity\Detable;
use SwoftTest\Db\Testing\Entity\User;

/**
 * DbTestCache
 */
abstract class AbstractMysqlCase extends AbstractTestCase
{
    public function addUsers()
    {
        $attrs = [
            'name' => 'name',
            'sex' => 1,
            'desc' => 'this my desc',
            'age' => mt_rand(1, 100),
        ];
        $user = new User();
        $user->fill($attrs);
        $id = $user->save()->getResult();

        $user = new User();
        $user->fill($attrs);
        $id2 = $user->save()->getResult();

        return [
            [[$id, $id2]],
        ];
    }

    public function addUser()
    {
        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));
        $id = $user->save()->getResult();

        return [
            [$id],
        ];
    }

    public function addDetable()
    {
        $detable = new Detable();
        $detable->setShortName('');
        $detable->setUtime(date('Y-m-d H:i:s'));
        $detable->setBooks(12);

        $did = $detable->save()->getResult();

        return [
            [$did],
        ];
    }

    public function addUserAndCount()
    {
        $user = new User();
        $user->setName('name');
        $user->setSex(1);
        $user->setDesc('this my desc');
        $user->setAge(mt_rand(1, 100));
        $id = $user->save()->getResult();

        $count = new Count();
        $count->setUid($id);
        $count->setFans(mt_rand(1000, 10000));
        $count->setFollows(mt_rand(1000, 10000));
        $count->save()->getResult();

        return [
            [$id],
        ];
    }

    public function mysqlProviders()
    {
        return $this->addUsers();
    }

    public function mysqlProvider()
    {
        return $this->addUser();
    }

    public function relationProider()
    {
        return $this->addUserAndCount();
    }

    public function detableProvider()
    {
        return $this->addDetable();
    }
}
