<?php

namespace Swoft\Task\Bean\Annotation;

/**
 * Task annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class Task
{
    /**
     * 任务名称
     *
     * @var string
     */
    private $name = '';

    /**
     * @var bool
     */
    private $coroutine = false;

    /**
     * Bean constructor.
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

        if (isset($values['coroutine'])) {
            $this->coroutine = $values['coroutine'];
        }
    }

    /**
     * 获取任务名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCoroutine(): bool
    {
        return $this->coroutine;
    }
}
