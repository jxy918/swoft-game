<?php

namespace SwoftTest\Db\Testing\Entity;

use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Model;

/**
 * @Entity()
 * @Table(name="keyword")
 * @uses      Keyword
 */
class Keyword extends Model
{
    /**
     * @var int $id
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $drop
     * @Column(name="drop", type="integer", default=0)
     */
    private $drop;

    /**
     * @var int $alert
     * @Column(name="alert", type="integer", default=0)
     */
    private $alert;

    /**
     * @var string $desc
     * @Column(name="desc", type="string", length=240)
     */
    private $desc;

    /**
     * @param int $value
     * @return $this
     */
    public function setId(int $value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setDrop(int $value): self
    {
        $this->drop = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setAlert(int $value): self
    {
        $this->alert = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDesc(string $value): self
    {
        $this->desc = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDrop()
    {
        return $this->drop;
    }

    /**
     * @return int
     */
    public function getAlert()
    {
        return $this->alert;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

}