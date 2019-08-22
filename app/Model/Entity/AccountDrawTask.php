<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 账户抽奖任务表
 * Class AccountDrawTask
 *
 * @since 2.0
 *
 * @Entity(table="account_draw_task")
 */
class AccountDrawTask extends Model
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
     * 抽奖任务ID
     *
     * @Column(name="task_id", prop="taskId")
     *
     * @var int
     */
    private $taskId;

    /**
     * 抽奖任务日期，UNIX时间
     *
     * @Column(name="task_date", prop="taskDate")
     *
     * @var int
     */
    private $taskDate;

    /**
     * 已完成次数
     *
     * @Column(name="finish_count", prop="finishCount")
     *
     * @var int
     */
    private $finishCount;

    /**
     * 当前抽奖任务阶段,从0开始
     *
     * @Column(name="current_stage", prop="currentStage")
     *
     * @var int
     */
    private $currentStage;

    /**
     * 是否已经领取奖励，1为已领取
     *
     * @Column(name="get_prize", prop="getPrize")
     *
     * @var int
     */
    private $getPrize;

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
     * @param int $taskId
     *
     * @return void
     */
    public function setTaskId(int $taskId): void
    {
        $this->taskId = $taskId;
    }

    /**
     * @param int $taskDate
     *
     * @return void
     */
    public function setTaskDate(int $taskDate): void
    {
        $this->taskDate = $taskDate;
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
     * @param int $currentStage
     *
     * @return void
     */
    public function setCurrentStage(int $currentStage): void
    {
        $this->currentStage = $currentStage;
    }

    /**
     * @param int $getPrize
     *
     * @return void
     */
    public function setGetPrize(int $getPrize): void
    {
        $this->getPrize = $getPrize;
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
    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    /**
     * @return int
     */
    public function getTaskDate(): ?int
    {
        return $this->taskDate;
    }

    /**
     * @return int
     */
    public function getFinishCount(): ?int
    {
        return $this->finishCount;
    }

    /**
     * @return int
     */
    public function getCurrentStage(): ?int
    {
        return $this->currentStage;
    }

    /**
     * @return int
     */
    public function getGetPrize(): ?int
    {
        return $this->getPrize;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

}
