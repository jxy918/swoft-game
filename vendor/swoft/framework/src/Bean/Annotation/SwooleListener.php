<?php

namespace Swoft\Bean\Annotation;

use Swoft\Bootstrap\SwooleEvent;

/**
 * the listener of swoole
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      SwooleListener
 * @version   2018年01月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SwooleListener
{
    /**
     * the events of listener
     *
     * @var array
     */
    private $event = [];

    /**
     * @var string
     */
    private $type = SwooleEvent::TYPE_SERVER;

    /**
     * @var int
     */
    private $order = 0;

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

        if (isset($values['type'])) {
            $this->type = $values['type'];
        }

        if (isset($values['order'])) {
            $this->order = $values['order'];
        }
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}
