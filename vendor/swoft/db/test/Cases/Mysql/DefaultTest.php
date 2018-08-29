<?php

namespace SwoftTest\Db\Cases\Mysql;

use Swoft\Db\Query;
use SwoftTest\Db\Cases\AbstractMysqlCase;
use SwoftTest\Db\Testing\Entity\Detable;

/**
 * DefaultTest
 */
class DefaultTest extends AbstractMysqlCase
{
    public function testAdd()
    {
        $detable = new Detable();
        $detable->setShortName('');
        $detable->setUtime(date('Y-m-d H:i:s'));
        $detable->setBooks(12);

        $did = $detable->save()->getResult();

        $this->assertTrue($did >= 1);

        $detable              = new Detable();
        $detable['shortName'] = '';
        $detable['utime']     = date('Y-m-d H:i:s');
        $detable['books']     = 12;

        $did2 = $detable->save()->getResult();
        $this->assertTrue($did2 >= 1);

        $detable = new Detable();

        $data              = [];
        $data['shortName'] = '';
        $data['utime']     = date('Y-m-d H:i:s');
        $data['books']     = 12;
        $data['dName']     = 'default name';

        $detable->fill($data);
        $did3 = $detable->save()->getResult();
        $this->assertTrue($did3 >= 1);
    }

    public function testAddByCo()
    {
        go(function () {
            $this->testAdd();
        });
    }

    /**
     * @expectedException \Swoft\Db\Exception\MysqlException
     */
    public function testAddRequireException()
    {
        $detable = new Detable();
        $detable->setShortName('');
        $detable->setUtime(date('Y-m-d H:i:s'));

        $did = $detable->save()->getResult();
    }

    public function testUpdate()
    {
        $detable = new Detable();
        $detable->setShortName('');
        $detable->setUtime(date('Y-m-d H:i:s'));
        $detable->setBooks(12);

        $did = $detable->save()->getResult();

        /* @var Detable $newDetable */
        $newDetable = Detable::findById($did)->getResult();
        $newDetable->setDName($setName = 'dname');
        $newDetable->setCount(10);
        $newDetable->setAmount($setAmount = 12.1);

        $result = $newDetable->update()->getResult();
        $this->assertEquals(1, $result);

        /* @var Detable $detable */
        $detable = Detable::findById($did)->getResult();
        $this->assertEquals($detable->getDName(), $setName);
        $this->assertEquals($detable['dAmount'], null);
        $this->assertEquals($detable['dCount'], null);
        $this->assertEquals($detable['amount'], $setAmount);

        $ret = Detable::updateOne(['d_name' => 'dname...'], ['s_id' => $did])->getResult();
        $this->assertEquals(1, $result);

        $detable = Detable::findById($did)->getResult();
        $this->assertEquals($detable['dName'], 'dname...');

    }

    public function testUpdateByCo()
    {
        go(function () {
            $this->testUpdate();
        });
    }

    public function testGet()
    {
        $time    = date('Y-m-d H:i:s');
        $detable = new Detable();
        $detable->setShortName('');
        $detable->setUtime($time);
        $detable->setBooks(12);

        $did = $detable->save()->getResult();

        $data = Detable::findById($did)->getResult();
        $this->assertEquals($data['utime'], $time);

        $data2 = Query::table(Detable::class)->where('s_id', $did)->one()->getResult();
        $this->assertEquals($data2['utime'], $time);
    }

    public function testGetByCo()
    {
        go(function () {
            $this->testGet();
        });
    }

}