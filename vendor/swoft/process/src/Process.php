<?php

namespace Swoft\Process;

use Swoft\App;
use Swoft\Helper\PhpHelper;
use Swoole\Process as SwooleProcess;

/**
 * The process
 */
class Process
{
    /**
     * @var SwooleProcess
     */
    private $process;

    /**
     * Process constructor.
     *
     * @param SwooleProcess  $process
     */
    public function __construct(SwooleProcess $process)
    {
        $this->process = $process;
    }

    /**
     * Start process
     *
     * @return mixed Create a successful PID to return to the child process, create a failure to return to false
     */
    public function start()
    {
        return $this->process->start();
    }

    /**
     * Set the name of process
     *
     * @param string $name
     */
    public function name(string $name)
    {
        if (!PhpHelper::isMac()) {
            $this->process->name($name);
        }
    }

    /**
     * Execute an external program
     *
     * @param string $execfile
     * @param array  $args
     *
     * @return bool
     */
    public function exec(string $execfile, array $args): bool
    {
        return $this->process->exec($execfile, $args);
    }

    /**
     * Write data into pipe
     *
     * @param string $data
     *
     * @return int
     */
    public function write(string $data): int
    {
        return $this->process->write($data);
    }

    /**
     * Read data from pipe
     *
     * @param int $bufferSize
     *
     * @return bool|string
     */
    public function read(int $bufferSize = 8192)
    {
        return $this->process->read($bufferSize);
    }

    /**
     * Sett the timeout of the pipe read or write
     *
     * @param float $timeout
     */
    public function setTimeout(double $timeout)
    {
        $this->process->setTimeout($timeout);
    }

    /**
     * Enable message queuing as interprocess communication
     *
     * @param int $msgkey
     * @param int $mode
     */
    public function useQueue(int $msgkey = 0, int $mode = 2)
    {
        $this->process->useQueue($msgkey, $mode);
    }

    /**
     * Show the message queue status
     *
     * @return array
     */
    public function statQueue(): array
    {
        return $this->process->statQueue();
    }

    /**
     * Remove message queue
     */
    public function freeQueue()
    {
        $this->process->freeQueue();
    }

    /**
     * Send data to the message queue
     *
     * @param string $data
     *
     * @return bool
     */
    public function push(string $data): bool
    {
        return $this->process->push($data);
    }

    /**
     * Get data from message queue
     *
     * @param int $maxSize
     *
     * @return string
     */
    public function pop(int $maxSize = 8192): string
    {
        return $this->process->pop($maxSize);
    }

    /**
     * Close the pipe
     *
     * @param int $which
     *
     * @return bool
     */
    public function close(int $which = 0): bool
    {
        return $this->process->close($which);
    }

    /**
     * Exit child process
     *
     * @param int $status
     *
     * @return int
     */
    public function exit(int $status = 0): int
    {
        return $this->process->exit($status);
    }

    /**
     * @param int $pid
     * @param int $signo
     *
     * @return bool
     */
    public static function kill(int $pid, int $signo = SIGTERM): bool
    {
        return SwooleProcess::kill($pid, $signo);
    }

    /**
     * @param bool $blocking
     *
     * @return array|bool
     */
    public static function wait(bool $blocking = true)
    {
        return SwooleProcess::wait($blocking);
    }

    /**
     * @param bool $nochdir
     * @param bool $noclose
     *
     */
    public static function daemon(bool $nochdir = false, bool $noclose = false)
    {
        SwooleProcess::daemon($nochdir, $noclose);
    }

    /**
     * @param int      $signo
     * @param callable $callback
     */
    public static function signal(int $signo, callable $callback)
    {
        SwooleProcess::signal($signo, $callback);
    }

    /**
     * @param int $intervalUsec
     * @param int $type
     *
     * @return bool
     */
    public static function alarm(int $intervalUsec, int $type = 0): bool
    {
        return SwooleProcess::alarm($intervalUsec, $type);
    }

    /**
     * @param array $cpuSet
     *
     * @return bool
     */
    public static function setaffinity(array $cpuSet): bool
    {
        return SwooleProcess::setaffinity($cpuSet);
    }

    /**
     * @return \Swoole\Process
     */
    public function getProcess(): SwooleProcess
    {
        return $this->process;
    }
}