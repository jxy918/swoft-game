<?php

namespace Swoft\Cache;

use Swoft\Core\AbstractResult;

/**
 * The result of cor
 */
class CacheCoResult extends AbstractResult
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params)
    {
        return $this->recv(true);
    }
}