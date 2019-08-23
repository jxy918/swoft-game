<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class AccountActivity
 *
 * @since 2.0
 *
 * @Entity(table="account_activity")
 */
class AccountActivity extends Model
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
     * 玩家ID
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 日活跃度
     *
     * @Column(name="activity_day", prop="activityDay")
     *
     * @var int
     */
    private $activityDay;

    /**
     * 天活跃刷新时间19010101刷新格式
     *
     * @Column(name="activity_day_time_upd", prop="activityDayTimeUpd")
     *
     * @var int|null
     */
    private $activityDayTimeUpd;

    /**
     * 周活跃度
     *
     * @Column(name="activity_week", prop="activityWeek")
     *
     * @var int
     */
    private $activityWeek;

    /**
     * 天奖励领取状态
     *
     * @Column(name="day_prize_tag", prop="dayPrizeTag")
     *
     * @var int
     */
    private $dayPrizeTag;

    /**
     * 周奖励领取状态
     *
     * @Column(name="week_prize_tag", prop="weekPrizeTag")
     *
     * @var int
     */
    private $weekPrizeTag;

    /**
     * 刷新时间
     *
     * @Column()
     *
     * @var string
     */
    private $updtime;


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
     * @param int $activityDay
     *
     * @return void
     */
    public function setActivityDay(int $activityDay): void
    {
        $this->activityDay = $activityDay;
    }

    /**
     * @param int|null $activityDayTimeUpd
     *
     * @return void
     */
    public function setActivityDayTimeUpd(?int $activityDayTimeUpd): void
    {
        $this->activityDayTimeUpd = $activityDayTimeUpd;
    }

    /**
     * @param int $activityWeek
     *
     * @return void
     */
    public function setActivityWeek(int $activityWeek): void
    {
        $this->activityWeek = $activityWeek;
    }

    /**
     * @param int $dayPrizeTag
     *
     * @return void
     */
    public function setDayPrizeTag(int $dayPrizeTag): void
    {
        $this->dayPrizeTag = $dayPrizeTag;
    }

    /**
     * @param int $weekPrizeTag
     *
     * @return void
     */
    public function setWeekPrizeTag(int $weekPrizeTag): void
    {
        $this->weekPrizeTag = $weekPrizeTag;
    }

    /**
     * @param string $updtime
     *
     * @return void
     */
    public function setUpdtime(string $updtime): void
    {
        $this->updtime = $updtime;
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
    public function getActivityDay(): ?int
    {
        return $this->activityDay;
    }

    /**
     * @return int|null
     */
    public function getActivityDayTimeUpd(): ?int
    {
        return $this->activityDayTimeUpd;
    }

    /**
     * @return int
     */
    public function getActivityWeek(): ?int
    {
        return $this->activityWeek;
    }

    /**
     * @return int
     */
    public function getDayPrizeTag(): ?int
    {
        return $this->dayPrizeTag;
    }

    /**
     * @return int
     */
    public function getWeekPrizeTag(): ?int
    {
        return $this->weekPrizeTag;
    }

    /**
     * @return string
     */
    public function getUpdtime(): ?string
    {
        return $this->updtime;
    }

}
