<?php

namespace Swoft\Process\Bean\Annotation;

/**
 * the process annotation of bootstrap
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      Process
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Process
{
    /**
     * @var string
     */
    private $name = "";

    /**
     * @var bool
     */
    private $boot = false;

    /**
     * @var bool
     */
    private $inout = false;

    /**
     * @var bool
     */
    private $pipe = true;

    /**
     * @var bool
     */
    private $coroutine = false;

    /**
     * @var int
     */
    private $num = 1;

    /**
     * Process constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        if (isset($values['name'])) {
            $this->name = $values['name'];
        }

        if (isset($values['num'])) {
            $this->num = $values['num'];
        }

        if (isset($values['boot'])) {
            $this->boot = $values['boot'];
        }

        if (isset($values['inout'])) {
            $this->inout = $values['inout'];
        }

        if (isset($values['pipe'])) {
            $this->pipe = $values['pipe'];
        }

        if (isset($values['coroutine'])) {
            $this->coroutine = $values['coroutine'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * @return bool
     */
    public function isBoot(): bool
    {
        return $this->boot;
    }

    /**
     * @return bool
     */
    public function isInout(): bool
    {
        return $this->inout;
    }

    /**
     * @return bool
     */
    public function isPipe(): bool
    {
        return $this->pipe;
    }

    /**
     * @return bool
     */
    public function isCoroutine(): bool
    {
        return $this->coroutine;
    }
}