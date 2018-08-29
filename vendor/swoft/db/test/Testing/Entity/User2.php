<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Db\Testing\Entity;

use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Model;
use Swoft\Db\Types;

/**
 * @Entity()
 * @Table(name="user")
 */
class User2 extends Model
{
    /**
     * @Id()
     * @Column(name="id", type=Types::INT)
     * @var null|int
     */
    private $id;

    /**
     * @Column(name="name", type=Types::STRING, length=20)
     * @var null|string
     */
    private $name;

    /**
     * @Column(name="age", type=Types::INT)
     * @var int
     */
    private $age;

    /**
     * 性别
     *
     * @Column(name="sex", type="int")
     * @var int
     */
    private $sex;

    /**
     * @Column(name="description", type="string")
     * @var string
     */
    private $desc;

    /**
     * @var mixed
     */
    private $otherProperty;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param int $sex
     */
    public function setSex(int $sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     */
    public function setDesc(string $desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return mixed
     */
    public function getOtherProperty()
    {
        return $this->otherProperty;
    }

    /**
     * @param mixed $otherProperty
     */
    public function setOtherProperty($otherProperty)
    {
        $this->otherProperty = $otherProperty;
    }
}
