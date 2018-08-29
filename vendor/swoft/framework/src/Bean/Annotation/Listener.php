<?php

namespace Swoft\Bean\Annotation;

/**
 * 监听器注解
 *
 * @Annotation
 * @Target("CLASS")
 */
class Listener
{
    /**
     * 监听事件名称
     *
     * @var string
     */
    private $event = '';

    /**
     * AutoController constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->event = $values['value'];
        }

        if (isset($values['event'])) {
            $this->event = $values['event'];
        }
    }

    /**
     * 监听事件
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }
}
