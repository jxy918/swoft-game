<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class AccountAddress
 *
 * @since 2.0
 *
 * @Entity(table="account_address")
 */
class AccountAddress extends Model
{
    /**
     * 用户id
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 联系人
     *
     * @Column()
     *
     * @var string
     */
    private $contact;

    /**
     * 手机号码
     *
     * @Column()
     *
     * @var string
     */
    private $mobile;

    /**
     * 联系地址
     *
     * @Column()
     *
     * @var string
     */
    private $address;


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
     * @param string $contact
     *
     * @return void
     */
    public function setContact(string $contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @param string $mobile
     *
     * @return void
     */
    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @param string $address
     *
     * @return void
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @return string
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

}
