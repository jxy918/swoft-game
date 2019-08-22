<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class AccountExt
 *
 * @since 2.0
 *
 * @Entity(table="account_ext")
 */
class AccountExt extends Model
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
     * 
     *
     * @Column(name="client_id", prop="clientId")
     *
     * @var int
     */
    private $clientId;

    /**
     * 1安卓、2IOS
     *
     * @Column()
     *
     * @var int
     */
    private $os;

    /**
     * 注册时游戏版本
     *
     * @Column(name="game_version", prop="gameVersion")
     *
     * @var string
     */
    private $gameVersion;

    /**
     * 注册时间
     *
     * @Column(name="register_time", prop="registerTime")
     *
     * @var string
     */
    private $registerTime;

    /**
     * 账户类型 1游客 2正式账户 100机器人
     *
     * @Column(name="account_type", prop="accountType")
     *
     * @var int
     */
    private $accountType;

    /**
     * 封号时间(小于当前时间表示未被封号)
     *
     * @Column(name="banned_time", prop="bannedTime")
     *
     * @var string
     */
    private $bannedTime;

    /**
     * 第三方ID
     *
     * @Column(name="third_id", prop="thirdId")
     *
     * @var string
     */
    private $thirdId;

    /**
     * 最后登录时间
     *
     * @Column(name="last_logon_time", prop="lastLogonTime")
     *
     * @var string
     */
    private $lastLogonTime;

    /**
     * 上一次登录时间(与last_logon_time不同天)
     *
     * @Column(name="prev_logon_time", prop="prevLogonTime")
     *
     * @var string
     */
    private $prevLogonTime;

    /**
     * 上上次登录时间(与prev_logon_time不同天)
     *
     * @Column(name="prev_logon_time2", prop="prevLogonTime2")
     *
     * @var string
     */
    private $prevLogonTime2;

    /**
     * 是否为当天首次登录
     *
     * @Column(name="is_first_logon", prop="isFirstLogon")
     *
     * @var int
     */
    private $isFirstLogon;

    /**
     * 用户来源,0=默认值
     *
     * @Column(name="account_source", prop="accountSource")
     *
     * @var int
     */
    private $accountSource;


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
     * @param int $clientId
     *
     * @return void
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param int $os
     *
     * @return void
     */
    public function setOs(int $os): void
    {
        $this->os = $os;
    }

    /**
     * @param string $gameVersion
     *
     * @return void
     */
    public function setGameVersion(string $gameVersion): void
    {
        $this->gameVersion = $gameVersion;
    }

    /**
     * @param string $registerTime
     *
     * @return void
     */
    public function setRegisterTime(string $registerTime): void
    {
        $this->registerTime = $registerTime;
    }

    /**
     * @param int $accountType
     *
     * @return void
     */
    public function setAccountType(int $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * @param string $bannedTime
     *
     * @return void
     */
    public function setBannedTime(string $bannedTime): void
    {
        $this->bannedTime = $bannedTime;
    }

    /**
     * @param string $thirdId
     *
     * @return void
     */
    public function setThirdId(string $thirdId): void
    {
        $this->thirdId = $thirdId;
    }

    /**
     * @param string $lastLogonTime
     *
     * @return void
     */
    public function setLastLogonTime(string $lastLogonTime): void
    {
        $this->lastLogonTime = $lastLogonTime;
    }

    /**
     * @param string $prevLogonTime
     *
     * @return void
     */
    public function setPrevLogonTime(string $prevLogonTime): void
    {
        $this->prevLogonTime = $prevLogonTime;
    }

    /**
     * @param string $prevLogonTime2
     *
     * @return void
     */
    public function setPrevLogonTime2(string $prevLogonTime2): void
    {
        $this->prevLogonTime2 = $prevLogonTime2;
    }

    /**
     * @param int $isFirstLogon
     *
     * @return void
     */
    public function setIsFirstLogon(int $isFirstLogon): void
    {
        $this->isFirstLogon = $isFirstLogon;
    }

    /**
     * @param int $accountSource
     *
     * @return void
     */
    public function setAccountSource(int $accountSource): void
    {
        $this->accountSource = $accountSource;
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
    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    /**
     * @return int
     */
    public function getOs(): ?int
    {
        return $this->os;
    }

    /**
     * @return string
     */
    public function getGameVersion(): ?string
    {
        return $this->gameVersion;
    }

    /**
     * @return string
     */
    public function getRegisterTime(): ?string
    {
        return $this->registerTime;
    }

    /**
     * @return int
     */
    public function getAccountType(): ?int
    {
        return $this->accountType;
    }

    /**
     * @return string
     */
    public function getBannedTime(): ?string
    {
        return $this->bannedTime;
    }

    /**
     * @return string
     */
    public function getThirdId(): ?string
    {
        return $this->thirdId;
    }

    /**
     * @return string
     */
    public function getLastLogonTime(): ?string
    {
        return $this->lastLogonTime;
    }

    /**
     * @return string
     */
    public function getPrevLogonTime(): ?string
    {
        return $this->prevLogonTime;
    }

    /**
     * @return string
     */
    public function getPrevLogonTime2(): ?string
    {
        return $this->prevLogonTime2;
    }

    /**
     * @return int
     */
    public function getIsFirstLogon(): ?int
    {
        return $this->isFirstLogon;
    }

    /**
     * @return int
     */
    public function getAccountSource(): ?int
    {
        return $this->accountSource;
    }

}
