<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Bean;

/**
 *
 *
 * @uses      ProxyBase
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProxyBase
{
    public function publicFun1Base($p1, $p2)
    {
        return $p1 . $p2;
    }

    public function publicFun2Base(string $p1, $p2, $float = 1.2)
    {
        return $p1 . $p2. $float;
    }

    public function publicFun3Base(string $p1, $p2, $string = 'string'): string
    {
        return $p1 . $p2. $string;
    }

    protected function protectedFun1Base($p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun2Base(string $p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun3Base(string $p1, $p2): string
    {
        return $p1 . $p2;
    }

    public function publicFun1($p1, $p2)
    {
        return $p1 . $p2;
    }

    public function publicFun2(string $p1, $p2)
    {
        return $p1 . $p2;
    }

    public function publicFun3(string $p1, $p2): string
    {
        return $p1 . $p2;
    }

    protected function protectedFun1($p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun2(string $p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun3(string $p1, $p2): string
    {
        return $p1 . $p2;
    }
}
