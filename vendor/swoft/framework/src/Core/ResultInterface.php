<?php

namespace Swoft\Core;

/**
 * The interface of result
 */
interface ResultInterface
{
    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function getResult(...$params);
}
