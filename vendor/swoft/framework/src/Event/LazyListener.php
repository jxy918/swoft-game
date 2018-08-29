<?php

namespace Swoft\Event;

use Swoft\Helper\PhpHelper;

/**
 * Class LazyListener - 将callable包装成对象
 * @package Swoft\Event
 * @author    inhere <in.798@qq.com>
 */
class LazyListener implements EventHandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function handle(EventInterface $event)
    {
        return PhpHelper::call($this->callback, $event);
    }

    /**
     * @return callable|mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
