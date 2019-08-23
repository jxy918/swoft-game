<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 账户状态表
 * Class AccountState
 *
 * @since 2.0
 *
 * @Entity(table="account_state")
 */
class AccountState extends Model
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
     * 
     *
     * @Column(name="state_id", prop="stateId")
     *
     * @var int
     */
    private $stateId;

    /**
     * 
     *
     * @Column(name="state_value", prop="stateValue")
     *
     * @var int
     */
    private $stateValue;

    /**
     * 过期时间(UNIX时间)
     *
     * @Column(name="expiry_time", prop="expiryTime")
     *
     * @var int
     */
    private $expiryTime;

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
     * @param int $stateId
     *
     * @return void
     */
    public function setStateId(int $stateId): void
    {
        $this->stateId = $stateId;
    }

    /**
     * @param int $stateValue
     *
     * @return void
     */
    public function setStateValue(int $stateValue): void
    {
        $this->stateValue = $stateValue;
    }

    /**
     * @param int $expiryTime
     *
     * @return void
     */
    public function setExpiryTime(int $expiryTime): void
    {
        $this->expiryTime = $expiryTime;
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
    public function getStateId(): ?int
    {
        return $this->stateId;
    }

    /**
     * @return int
     */
    public function getStateValue(): ?int
    {
        return $this->stateValue;
    }

    /**
     * @return int
     */
    public function getExpiryTime(): ?int
    {
        return $this->expiryTime;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

}
