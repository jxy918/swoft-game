<?php

namespace Swoft\Session\Handler;


interface LifetimeInterface
{

    /**
     * @param int $seconds
     * @return $this
     */
    public function setLifetime(int $seconds);

    /**
     * @return int
     */
    public function getLifetime(): int;

}