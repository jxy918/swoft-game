<?php

namespace Swoft\Task;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Task\Exception\TaskException;

/**
 * QueueTask
 *
 * @Bean()
 */
class QueueTask
{
    /**
     * @var int
     */
    private $messageKey = 0x70001001;

    /**
     * @var string
     */
    private $tmp = '/tmp/';

    /**
     * @var string
     */
    private $tmpFile = 'swoft.task';

    /**
     * @var int
     */
    private $workerNum = 1;

    /**
     * @var int
     */
    private $taskNum = 1;

    /**
     * @var mixed
     */
    private $queueId = null;

    /**
     * @var int
     */
    private $taskId = 0;

    /**
     * Tmp file
     */
    const SW_TASK_TMPFILE = 1;

    /**
     * Php serialize
     */
    const SW_TASK_SERIALIZE = 2;

    /**
     * Task block
     */
    const SW_TASK_NONBLOCK = 4;

    /**
     * Event
     */
    const SW_EVENT_TASK = 7;

    /**
     * Init
     */
    public function init()
    {
        $setting = App::$appProperties['server']['setting'];

        $this->tmp        = $setting['task_tmpdir'];
        $this->workerNum  = $setting['worker_num'];
        $this->taskNum    = $setting['task_worker_num'];
        $this->messageKey = $setting['message_queue_key'];
    }

    /**
     * @param string $data
     * @param int    $taskWorkerId
     * @param int    $srcWorkerId
     *
     * @return bool
     */
    public function deliver(string $data, int $taskWorkerId = null, $srcWorkerId = null)
    {
        if ($taskWorkerId === null) {
            $taskWorkerId = mt_rand($this->workerNum + 1, $this->workerNum + $this->taskNum);
        }

        if ($srcWorkerId === null) {
            $srcWorkerId = mt_rand(0, $this->workerNum - 1);
        }

        $this->check();
        $data   = $this->pack($data, $srcWorkerId);
        $result = \msg_send($this->queueId, $taskWorkerId, $data, false);
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * @throws TaskException
     */
    private function check()
    {
        if (!function_exists('msg_get_queue')) {
            throw new TaskException('You must to compiled php with --enable-sysvmsg');
        }
        if ($this->queueId === null) {
            $this->queueId = \msg_get_queue((int)$this->messageKey);
        }

        if (empty($this->queueId)) {
            throw new TaskException(sprintf('msg_get_queue() failed. messageKey=%s', $this->messageKey));
        }
    }

    /**
     * @param string $data
     * @param string $srcWorkerId
     *
     * @return string
     */
    private function pack(string $data, string $srcWorkerId): string
    {
        $flags = self::SW_TASK_NONBLOCK;
        $type  = self::SW_EVENT_TASK;
        if (!is_string($data)) {
            $data  = serialize($data);
            $flags |= self::SW_TASK_SERIALIZE;
        }
        if (strlen($data) >= 8180) {
            $tmpFile = tempnam($this->tmp, $this->tmpFile);
            file_put_contents($tmpFile, $data);
            $data  = pack('l', strlen($data)) . $tmpFile . "\0";
            $flags |= self::SW_TASK_TMPFILE;
            $len   = 128 + 24;
        } else {
            $len = strlen($data);
        }

        return pack('lSsCCS', $this->taskId++, $len, $srcWorkerId, $type, 0, $flags) . $data;
    }
}