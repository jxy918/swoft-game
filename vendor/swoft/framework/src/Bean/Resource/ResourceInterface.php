<?php

namespace Swoft\Bean\Resource;

/**
 * Resource Interface
 */
interface ResourceInterface
{
    /**
     * 获取已解析的配置beans
     *
     * @return array
     */
    public function getDefinitions();
}
