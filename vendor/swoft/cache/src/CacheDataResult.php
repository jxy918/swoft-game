<?php

namespace Swoft\Cache;

use Swoft\Core\AbstractResult;

/**
 * CacheDataResult
 */
class CacheDataResult extends AbstractResult
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        $this->release();
        return $this->result;
    }
}