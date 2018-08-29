<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Swoft\DataParser;

/**
 * Class PhpParser
 * @package Swoft\DataParser
 * @author inhere <in.798@qq.com>
 */
class PhpParser implements ParserInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return \serialize($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        return \unserialize($data, ['allowed_classes' => false]);
    }
}
