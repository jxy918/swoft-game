<?php declare(strict_types=1);


namespace Swoft\Process;


use Swoft\Process\Contract\UserProcessInterface;

/**
 * Class UserProcess
 *
 * @since 2.0
 */
abstract class UserProcess implements UserProcessInterface
{
    /**
     * @var bool
     */
    protected $stdinOut = false;

    /**
     * @var int
     */
    protected $pipeType = 2;

    /**
     * @var bool
     */
    protected $coroutine = true;

    /**
     * @return bool
     */
    public function isStdinOut(): bool
    {
        return $this->stdinOut;
    }

    /**
     * @return int
     */
    public function getPipeType(): int
    {
        return $this->pipeType;
    }

    /**
     * @return bool
     */
    public function isCoroutine(): bool
    {
        return $this->coroutine;
    }
}