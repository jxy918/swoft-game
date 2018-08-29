<?php

namespace Swoft\Session\Handler;


trait LifetimeTrait
{

    /**
     * @var int
     */
    protected $lifetime = 0;

    /**
     * @param int $seconds
     * @return $this
     */
    public function setLifetime(int $seconds)
    {
        $this->lifetime = $seconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

}