<?php declare(strict_types=1);


namespace Swoft\Process;


use Swoft\Process\Exception\ProcessException;
use Swoole\Coroutine\Socket;
use Swoole\Process as SwooleProcess;

/**
 * Class Process
 *
 * @since 2.0
 */
class Process
{
    /**
     * @var SwooleProcess
     */
    protected $process;

    /**
     * @param SwooleProcess $process
     *
     * @return Process
     * @throws ProcessException
     */
    public static function new(SwooleProcess $process): self
    {
        $self = new self(null, false, 2, true, $process);

        $self->process = $process;
        return $self;
    }

    /**
     * Process constructor.
     *
     * @param callable           $callback
     * @param bool               $inout
     * @param int                $pipeType
     * @param bool               $coroutine
     * @param SwooleProcess|null $process
     *
     * @throws ProcessException
     */
    public function __construct(
        callable $callback = null,
        bool $inout = false,
        int $pipeType = 2,
        bool $coroutine = true,
        SwooleProcess $process = null
    ) {
        if (!empty($process)) {
            $this->process = $process;
            return;
        }

        if (empty($callback)) {
            throw new ProcessException('Process callback must be not empty!');
        }

        $this->process = new SwooleProcess($callback, $inout, $pipeType, $coroutine);
    }

    /**
     * @return int
     * @throws ProcessException
     */
    public function start(): int
    {
        $result = $this->process->start();
        if ($result === false) {
            throw new ProcessException('Process start fail!');
        }

        return $result;
    }

    /**
     * @param string $name
     */
    public function name(string $name): void
    {
        $this->process->name($name);
    }

    /**
     * @param string $shell
     * @param array  $args
     */
    public function exec(string $shell, array $args): void
    {
        $this->process->exec($shell, $args);
    }

    /**
     * @param string $data
     *
     * @return int
     * @throws ProcessException
     */
    public function write(string $data): int
    {
        $result = $this->process->write($data);
        if ($result !== false) {
            return (int)$result;
        }

        $error = $this->getError();
        throw new ProcessException(sprintf('Process write fail!(%s)', $error));
    }

    /**
     * @param int $bufferSize
     *
     * @return string
     * @throws ProcessException
     */
    public function read(int $bufferSize = 8192): string
    {
        $result = $this->process->read($bufferSize);
        if ($result === false) {
            throw new ProcessException('Process read file');
        }

        return (string)$result;
    }

    /**
     * @param float $seconds
     *
     * @return bool
     */
    public function setTimeout(float $seconds): bool
    {
        return (bool)$this->process->setTimeout($seconds);
    }

    /**
     * @param bool $blocking
     *
     * @return bool
     */
    public function setBlocking(bool $blocking = true): bool
    {
        return (bool)$this->process->setBlocking($blocking);
    }

    /**
     * @param int $msgkey
     * @param int $mode
     * @param int $capacity
     *
     * @return bool
     */
    public function useQueue(int $msgkey = 0, int $mode = 2, int $capacity = 8192): bool
    {
        return $this->process->useQueue($msgkey, $mode, $capacity);
    }

    /**
     * @return array
     */
    public function statQueue(): array
    {
        return $this->process->statQueue();
    }

    /**
     * @return bool
     */
    public function freeQueue(): bool
    {
        return (bool)$this->process->freeQueue();
    }

    /**
     * @return Socket
     */
    public function exportSocket(): Socket
    {
        return $this->process->exportSocket();
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    public function push(string $data): bool
    {
        return $this->process->push($data);
    }

    /**
     * @param int $maxSize
     *
     * @return string
     * @throws ProcessException
     */
    public function pop(int $maxSize = 8192): string
    {
        $result = $this->process->pop($maxSize);
        if ($result !== false) {
            return (string)$result;
        }

        $error = $this->getError();
        throw new ProcessException($error);
    }

    /**
     * @param int $which
     *
     * @return bool
     */
    public function close(int $which = 0): bool
    {
        return (bool)$this->process->close($which);
    }

    /**
     * @param int $status
     *
     * @return int
     */
    public function exit(int $status = 0): int
    {
        return (int)$this->process->exit($status);
    }

    /**
     * @param int $pid
     * @param int $signo
     *
     * @return bool
     */
    public static function kill(int $pid, $signo = 15): bool
    {
        return (bool)SwooleProcess::kill($pid, $signo);
    }

    /**
     * @param bool $blocking
     *
     * @return array
     * @throws ProcessException
     */
    public static function wait(bool $blocking = true): array
    {
        $result = SwooleProcess::wait($blocking);
        if ($result !== $result) {
            return (array)$result;
        }

        throw new ProcessException(sprintf('Process wait fail!'));
    }

    /**
     * @param bool $nochDir
     * @param bool $noClose
     *
     * @return bool
     */
    public static function daemon(bool $nochDir = false, bool $noClose = false): bool
    {
        return (bool)SwooleProcess::daemon($nochDir, $noClose);
    }

    /**
     * @param int           $signo
     * @param callable|null $callback
     *
     * @return bool
     */
    public static function signal(int $signo, callable $callback = null)
    {
        return (bool)SwooleProcess::signal($signo, $callback);
    }

    /**
     * @param int $intervalUsec
     * @param int $type
     *
     * @return bool
     */
    public static function alarm(int $intervalUsec, int $type = 0): bool
    {
        return (bool)SwooleProcess::alarm($intervalUsec, $type);
    }

    /**
     * @param array $cpuSet
     *
     * @return bool
     */
    public static function setAffinity(array $cpuSet): bool
    {
        return (bool)SwooleProcess::setAffinity($cpuSet);
    }

    /**
     * @return string
     */
    private function getError(): string
    {
        $errno = swoole_errno();
        return (string)swoole_strerror($errno);
    }
}