<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 用户牌型数据
 * Class AccountCard
 *
 * @since 2.0
 *
 * @Entity(table="account_card")
 */
class AccountCard extends Model
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
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 牌型ID
     *
     * @Column(name="card_id", prop="cardId")
     *
     * @var int
     */
    private $cardId;

    /**
     * 牌型日期，如20180523
     *
     * @Column(name="card_date", prop="cardDate")
     *
     * @var int
     */
    private $cardDate;

    /**
     * 已完成次数
     *
     * @Column(name="finish_count", prop="finishCount")
     *
     * @var int
     */
    private $finishCount;

    /**
     * 
     *
     * @Column(name="last_update_time", prop="lastUpdateTime")
     *
     * @var string
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
     * @param int $uid
     *
     * @return void
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @param int $cardId
     *
     * @return void
     */
    public function setCardId(int $cardId): void
    {
        $this->cardId = $cardId;
    }

    /**
     * @param int $cardDate
     *
     * @return void
     */
    public function setCardDate(int $cardDate): void
    {
        $this->cardDate = $cardDate;
    }

    /**
     * @param int $finishCount
     *
     * @return void
     */
    public function setFinishCount(int $finishCount): void
    {
        $this->finishCount = $finishCount;
    }

    /**
     * @param string $lastUpdateTime
     *
     * @return void
     */
    public function setLastUpdateTime(string $lastUpdateTime): void
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
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return int
     */
    public function getCardId(): ?int
    {
        return $this->cardId;
    }

    /**
     * @return int
     */
    public function getCardDate(): ?int
    {
        return $this->cardDate;
    }

    /**
     * @return int
     */
    public function getFinishCount(): ?int
    {
        return $this->finishCount;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

}
