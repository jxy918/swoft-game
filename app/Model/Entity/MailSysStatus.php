<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 系统邮件阅读状态，用于关联mail_sys表的邮件
 * Class MailSysStatus
 *
 * @since 2.0
 *
 * @Entity(table="mail_sys_status")
 */
class MailSysStatus extends Model
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
     * 邮件ID，关联mail_sys表的id
     *
     * @Column(name="mail_id", prop="mailId")
     *
     * @var int
     */
    private $mailId;

    /**
     * 收件人uid
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

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
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $mailId
     *
     * @return void
     */
    public function setMailId(int $mailId): void
    {
        $this->mailId = $mailId;
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getMailId(): ?int
    {
        return $this->mailId;
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

}
