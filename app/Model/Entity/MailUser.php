<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 用户邮件
 * Class MailUser
 *
 * @since 2.0
 *
 * @Entity(table="mail_user")
 */
class MailUser extends Model
{
    /**
     * 个人邮件的ID要大于1亿
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 发送时间
     *
     * @Column(name="send_time", prop="sendTime")
     *
     * @var string
     */
    private $sendTime;

    /**
     * 收件人uid
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 0为系统邮件
     *
     * @Column(name="from_uid", prop="fromUid")
     *
     * @var int
     */
    private $fromUid;

    /**
     * 邮件类型: 1.跑马灯消息 2.通知邮件（系统消息邮件、更新通知邮件） 3.奖励邮件
     *
     * @Column(name="type_id", prop="typeId")
     *
     * @var int
     */
    private $typeId;

    /**
     * 邮件状态，1为有效
     *
     * @Column(name="is_valid", prop="isValid")
     *
     * @var int
     */
    private $isValid;

    /**
     * 阅读(领取附件)时间,20000101表示未阅读
     *
     * @Column(name="read_time", prop="readTime")
     *
     * @var string
     */
    private $readTime;

    /**
     * 阅读状态，0未阅读，1已读，2已领附件，3接收人删除
     *
     * @Column(name="read_status", prop="readStatus")
     *
     * @var int
     */
    private $readStatus;

    /**
     * 奖励ID, 0无奖励 1金币 2钻石 其它为gift配置表的ID
     *
     * @Column(name="gift_id", prop="giftId")
     *
     * @var int
     */
    private $giftId;

    /**
     * 奖励数量
     *
     * @Column(name="gift_count", prop="giftCount")
     *
     * @var int
     */
    private $giftCount;

    /**
     * 标题
     *
     * @Column()
     *
     * @var string
     */
    private $title;

    /**
     * 邮件内容
     *
     * @Column()
     *
     * @var string
     */
    private $content;


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
     * @param string $sendTime
     *
     * @return void
     */
    public function setSendTime(string $sendTime): void
    {
        $this->sendTime = $sendTime;
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
     * @param int $fromUid
     *
     * @return void
     */
    public function setFromUid(int $fromUid): void
    {
        $this->fromUid = $fromUid;
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
     * @param int $isValid
     *
     * @return void
     */
    public function setIsValid(int $isValid): void
    {
        $this->isValid = $isValid;
    }

    /**
     * @param string $readTime
     *
     * @return void
     */
    public function setReadTime(string $readTime): void
    {
        $this->readTime = $readTime;
    }

    /**
     * @param int $readStatus
     *
     * @return void
     */
    public function setReadStatus(int $readStatus): void
    {
        $this->readStatus = $readStatus;
    }

    /**
     * @param int $giftId
     *
     * @return void
     */
    public function setGiftId(int $giftId): void
    {
        $this->giftId = $giftId;
    }

    /**
     * @param int $giftCount
     *
     * @return void
     */
    public function setGiftCount(int $giftCount): void
    {
        $this->giftCount = $giftCount;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSendTime(): ?string
    {
        return $this->sendTime;
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
    public function getFromUid(): ?int
    {
        return $this->fromUid;
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
    public function getIsValid(): ?int
    {
        return $this->isValid;
    }

    /**
     * @return string
     */
    public function getReadTime(): ?string
    {
        return $this->readTime;
    }

    /**
     * @return int
     */
    public function getReadStatus(): ?int
    {
        return $this->readStatus;
    }

    /**
     * @return int
     */
    public function getGiftId(): ?int
    {
        return $this->giftId;
    }

    /**
     * @return int
     */
    public function getGiftCount(): ?int
    {
        return $this->giftCount;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

}
