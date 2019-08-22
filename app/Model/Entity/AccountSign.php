<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 用户签到奖励领取记录
 * Class AccountSign
 *
 * @since 2.0
 *
 * @Entity(table="account_sign")
 */
class AccountSign extends Model
{
    /**
     * 
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 签到日期(UNIX时间)
     *
     * @Column(name="sign_date", prop="signDate")
     *
     * @var int
     */
    private $signDate;

    /**
     * 连续签到天数
     *
     * @Column(name="continue_days", prop="continueDays")
     *
     * @var int
     */
    private $continueDays;

    /**
     * 本月签到天数
     *
     * @Column(name="month_days", prop="monthDays")
     *
     * @var int
     */
    private $monthDays;

    /**
     * 标识连续签到的索引
     *
     * @Column(name="continue_tag", prop="continueTag")
     *
     * @var int
     */
    private $continueTag;

    /**
     * 
     *
     * @Column(name="last_update_time", prop="lastUpdateTime")
     *
     * @var string
     */
    private $lastUpdateTime;


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
     * @param int $signDate
     *
     * @return void
     */
    public function setSignDate(int $signDate): void
    {
        $this->signDate = $signDate;
    }

    /**
     * @param int $continueDays
     *
     * @return void
     */
    public function setContinueDays(int $continueDays): void
    {
        $this->continueDays = $continueDays;
    }

    /**
     * @param int $monthDays
     *
     * @return void
     */
    public function setMonthDays(int $monthDays): void
    {
        $this->monthDays = $monthDays;
    }

    /**
     * @param int $continueTag
     *
     * @return void
     */
    public function setContinueTag(int $continueTag): void
    {
        $this->continueTag = $continueTag;
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
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return int
     */
    public function getSignDate(): ?int
    {
        return $this->signDate;
    }

    /**
     * @return int
     */
    public function getContinueDays(): ?int
    {
        return $this->continueDays;
    }

    /**
     * @return int
     */
    public function getMonthDays(): ?int
    {
        return $this->monthDays;
    }

    /**
     * @return int
     */
    public function getContinueTag(): ?int
    {
        return $this->continueTag;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

}
