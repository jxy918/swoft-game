<?php

namespace Swoft\Helper;

/**
 * the help of document
 *
 * @uses      DocumentHelper
 * @version   2018年01月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class DocumentHelper
{
    /**
     * 解析注解文档
     *
     * @param string $comment 注解文档
     *
     * @return array
     */
    public static function tagList(string $comment)
    {
        $comment = "@Description \n" . strtr(trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))), "\r", '');
        $parts   = preg_split('/^\s*@/m', $comment, -1, PREG_SPLIT_NO_EMPTY);

        $tags = [];
        foreach ($parts as $part) {
            $isMatch = preg_match('/^(\w+)(.*)/ms', trim($part), $matches);
            if ($isMatch == false) {
                continue;
            }
            $name = $matches[1];
            if (!isset($tags[$name])) {
                $tags[$name] = trim($matches[2]);
                continue;
            }
            if (is_array($tags[$name])) {
                $tags[$name][] = trim($matches[2]);
                continue;
            }
            $tags[$name] = [$tags[$name], trim($matches[2])];
        }

        return $tags;
    }
}