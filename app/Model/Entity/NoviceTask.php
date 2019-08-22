<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 新手任务表
 * Class NoviceTask
 *
 * @since 2.0
 *
 * @Entity(table="novice_task")
 */
class NoviceTask extends Model
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
     * 任务ID
     *
     * @Column(name="task_id", prop="taskId")
     *
     * @var int
     */
    private $taskId;

    /**
     * 任务日期，UNIX时间
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
     * 本轮已完成步数
     *
     * @Column(name="current_step", prop="currentStep")
     *
     * @var int
     */
    private $currentStep;

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
     * @param int $currentStep
     *
     * @return void
     */
    public function setCurrentStep(int $currentStep): void
    {
        $this->currentStep = $currentStep;
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
    public function getCurrentStep(): ?int
    {
        return $this->currentStep;
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
