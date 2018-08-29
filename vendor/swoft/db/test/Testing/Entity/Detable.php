<?php

namespace SwoftTest\Db\Testing\Entity;

use Swoft\Db\Model;
use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;

/**
 * @Entity()
 * @Table(name="detable")
 * @uses      Detable
 * @version   2018年05月03日
 */
class Detable extends Model
{
    /**
     * @var int $sId
     * @Id()
     * @Column(name="s_id", type="integer")
     */
    private $sId;

    /**
     * @var string $dName
     * @Column(name="d_name", type="string", length=20)
     */
    private $dName;

    /**
     * @var float $dAmount
     * @Column(name="d_amount", type="float", default="0")
     */
    private $dAmount;

    /**
     * @var int $dCount
     * @Column(name="d_count", type="integer", default="0")
     */
    private $dCount;

    /**
     * @var float $dnAmount
     * @Column(name="dn_amount", type="float")
     */
    private $dnAmount;

    /**
     * @var int $dnCount
     * @Column(name="dn_count", type="integer")
     */
    private $dnCount;

    /**
     * @var string $title
     * @Column(name="title", type="string", length=20, default="")
     */
    private $title;

    /**
     * @var int $count
     * @Column(name="count", type="integer", default="0")
     */
    private $count;

    /**
     * @var float $amount
     * @Column(name="amount", type="float", default="0")
     */
    private $amount;

    /**
     * @var int $books
     * @Column(name="books", type="integer")
     * @Required()
     */
    private $books;

    /**
     * @var string $shortName
     * @Column(name="short_name", type="string", length=20)
     * @Required()
     */
    private $shortName;

    /**
     * @var string $ctime
     * @Column(name="ctime", type="datetime", default="CURRENT_TIMESTAMP")
     */
    private $ctime;

    /**
     * @var string $utime
     * @Column(name="utime", type="datetime")
     * @Required()
     */
    private $utime;

    /**
     * @param int $value
     * @return $this
     */
    public function setSId(int $value)
    {
        $this->sId = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDName(string $value): self
    {
        $this->dName = $value;

        return $this;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setDAmount(float $value): self
    {
        $this->dAmount = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setDCount(int $value): self
    {
        $this->dCount = $value;

        return $this;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setDnAmount(float $value): self
    {
        $this->dnAmount = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setDnCount(int $value): self
    {
        $this->dnCount = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setCount(int $value): self
    {
        $this->count = $value;

        return $this;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setAmount(float $value): self
    {
        $this->amount = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setBooks(int $value): self
    {
        $this->books = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setShortName(string $value): self
    {
        $this->shortName = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCtime(string $value): self
    {
        $this->ctime = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUtime(string $value): self
    {
        $this->utime = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSId()
    {
        return $this->sId;
    }

    /**
     * @return mixed
     */
    public function getDName()
    {
        return $this->dName;
    }

    /**
     * @return mixed
     */
    public function getDAmount()
    {
        return $this->dAmount;
    }

    /**
     * @return mixed
     */
    public function getDCount()
    {
        return $this->dCount;
    }

    /**
     * @return mixed
     */
    public function getDnAmount()
    {
        return $this->dnAmount;
    }

    /**
     * @return mixed
     */
    public function getDnCount()
    {
        return $this->dnCount;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getCtime()
    {
        return $this->ctime;
    }

    /**
     * @return mixed
     */
    public function getUtime()
    {
        return $this->utime;
    }

}