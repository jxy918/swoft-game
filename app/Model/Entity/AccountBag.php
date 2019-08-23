<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 用户背包
 * Class AccountBag
 *
 * @since 2.0
 *
 * @Entity(table="account_bag")
 */
class AccountBag extends Model
{
    /**
     * 自增ID
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 用户uid
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 物品ID
     *
     * @Column(name="prop_id", prop="propId")
     *
     * @var int
     */
    private $propId;

    /**
     * 过期时间(UNIX时间)
     *
     * @Column(name="expiry_time", prop="expiryTime")
     *
     * @var int
     */
    private $expiryTime;

    /**
     * 物品数量
     *
     * @Column()
     *
     * @var int
     */
    private $quantity;

    /**
     * 是否是新添加，1为是
     *
     * @Column(name="is_new", prop="isNew")
     *
     * @var int
     */
    private $isNew;

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
     * @param int $propId
     *
     * @return void
     */
    public function setPropId(int $propId): void
    {
        $this->propId = $propId;
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
     * @param int $quantity
     *
     * @return void
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @param int $isNew
     *
     * @return void
     */
    public function setIsNew(int $isNew): void
    {
        $this->isNew = $isNew;
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
    public function getPropId(): ?int
    {
        return $this->propId;
    }

    /**
     * @return int
     */
    public function getExpiryTime(): ?int
    {
        return $this->expiryTime;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getIsNew(): ?int
    {
        return $this->isNew;
    }

    /**
     * @return string
     */
    public function getLastUpdateTime(): ?string
    {
        return $this->lastUpdateTime;
    }

}
