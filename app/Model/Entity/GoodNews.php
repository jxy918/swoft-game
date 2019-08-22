<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 跑马灯
 * Class GoodNews
 *
 * @since 2.0
 *
 * @Entity(table="good_news")
 */
class GoodNews extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 
     *
     * @Column(name="type_id", prop="typeId")
     *
     * @var int
     */
    private $typeId;

    /**
     * 排序号
     *
     * @Column(name="sort_id", prop="sortId")
     *
     * @var int
     */
    private $sortId;

    /**
     * 
     *
     * @Column()
     *
     * @var string
     */
    private $msg;

    /**
     * 
     *
     * @Column(name="last_update_time", prop="lastUpdateTime")
     *
     * @var int
     */
    private $lastUpdateTime;


    /**
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $typeId
     *
     * @return void
     */
    public function setTypeId(int $typeId): void
    {
        $this->typeId = $typeId;
    }

    /**
     * @param int $sortId
     *
     * @return void
     */
    public function setSortId(int $sortId): void
    {
        $this->sortId = $sortId;
    }

    /**
     * @param string $msg
     *
     * @return void
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @param int $lastUpdateTime
     *
     * @return void
     */
    public function setLastUpdateTime(int $lastUpdateTime): void
    {
        $this->lastUpdateTime = $lastUpdateTime;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    /**
     * @return int
     */
    public function getSortId(): ?int
    {
        return $this->sortId;
    }

    /**
     * @return string
     */
    public function getMsg(): ?string
    {
        return $this->msg;
    }

    /**
     * @return int
     */
    public function getLastUpdateTime(): ?int
    {
        return $this->lastUpdateTime;
    }

}
