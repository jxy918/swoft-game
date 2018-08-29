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
 * @uses      ProxyTrait
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
trait ProxyTrait
{
    public function __construct($c1, $c2)
    {
    }

    public function publicFun1Trait($p1, $p2)
    {
        return $p1 . $p2;
    }

    public function publicFun2Trait(string $p1, $p2)
    {
        return $p1 . $p2;
    }

    public function publicFun3Trait(string $p1, $p2): string
    {
        return $p1 . $p2;
    }

    protected function protectedFun1Trait($p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun2Trait(string $p1, $p2)
    {
        return $p1 . $p2;
    }

    protected function protectedFun3Trait(string $p1, $p2): string
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
