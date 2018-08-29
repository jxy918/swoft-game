<?php

namespace Swoft\Task\Event\Events;

use Swoft\Event\Event;

/**
 * 任务前置事件源
 *
 * @uses      BeforeTaskEvent
 * @version   2017年09月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeTaskEvent extends Event
{
    /**
     * 日志ID
     *
     * @var string
     */
    private $logid;

    /**
     * 跨度ID
     *
     * @var int
     */
    private $spanid;

    /**
     * 任务名称
     *
     * @var string
     */
    private $taskName;

    /**
     * 方法
     *
     * @var string
     */
    private $method;

    /**
     * 任务类型
     *
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $taskClass;

    /**
     * BeforeTaskEvent constructor.
     * @param null $name
     * @param string $logid
     * @param int $spanid
     * @param string $taskName
     * @param string $method
     * @param string $type
     * @param string $taskClass
     * @throws \InvalidArgumentException
     */
    public function __construct($name = null, string $logid, int $spanid, string $taskName, string $method, string $type, string $taskClass)
    {
        parent::__construct($name);

        $this->type = $type;
        $this->logid = $logid;
        $this->spanid = $spanid;
        $this->method = $method;
        $this->taskName = $taskName;
        $this->taskClass = $taskClass;
    }

    /**
     * @return string
     */
    public function getLogid(): string
    {
        return $this->logid;
    }

    /**
     * @return int
     */
    public function getSpanid(): int
    {
        return $this->spanid;
    }

    /**
     * @return string
     */
    public function getTaskName(): string
    {
        return $this->taskName;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTaskClass(): string
    {
        return $this->taskClass;
    }
}
