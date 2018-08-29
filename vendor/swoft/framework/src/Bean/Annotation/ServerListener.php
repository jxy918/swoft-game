<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/19
 * Time: 上午11:43
 */

namespace Swoft\Bean\Annotation;

/**
 * Class ServerListener
 * 跟 SwooleListener 差别不大，同样可用于监听Swoole的事件，是对 SwooleListener 的补充
 * 区别是：
 * - SwooleListener 中事件的监听器只允许一个，并且是直接注册到 swoole server上的(监听相同事件将会被覆盖)
 * - ServerListener 允许对swoole事件添加多个监听器，会逐个通知
 * - ServerListener 不影响 `/framework/src/Bootstrap/Server` 里基础swoole事件的绑定
 *
 * @package Swoft\Bean\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class ServerListener
{
    /**
     * the events of listener
     *
     * @var array
     */
    private $event;

    /**
     * AutoController constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->event = (array)$values['value'];
        }

        if (isset($values['event'])) {
            $this->event = (array)$values['event'];
        }
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->event ?: [];
    }
}
