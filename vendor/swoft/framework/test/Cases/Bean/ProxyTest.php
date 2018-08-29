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

use Swoft\Core\Config;

/**
 *
 *
 * @uses      ProxyTest
 * @version   2017年12月23日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProxyTest extends ProxyBase
{
    use ProxyTrait;

    public function __construct($c1, $c2)
    {
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
    }

    public static function staticMethod()
    {
    }

    public function publicReturnAd($p, &$data):Config
    {
        return $p;
    }

    public function publicReturnBool($p, $strategy = false):Config
    {
        return $p;
    }

    public function publicReturnBool2($p, $strategy = true):Config
    {
        return $p;
    }

    public function publicReturnObject2($p, $strategy = 'consul'):Config
    {
        return $p;
    }

    public function publicReturnObject($p, $strategy = 'consul'):self
    {
        return $p;
    }

    public function publicReturnObject3($p, $strategy = 'consul'):ProxyTest
    {
        return $p;
    }

    public function publicConst3($p, $strategy = 'consul')
    {
        return $p;
    }

    public function publicConst2($p, $strategy = __FILE__)
    {
        return $p;
    }

    public function publicConst($p, $strategy = \Swoft\Helper\DirHelper::SCAN_BFS)
    {
        return $p;
    }

    public function publicNull($p1, $deafult = null)
    {
        return $p1;
    }

    public function publicParams($p1, ...$params)
    {
        return $p1 . count($params);
    }

    public function publicFun1($p1, $p2, $ary = [])
    {
        return $p1 . $p2. count($ary);
    }

    public function publicFun2(string $p1, $p2, $ary = [1])
    {
        return $p1 . $p2. count($ary);
    }

    public function publicFun3(string $p1, $p2, $int = 1): string
    {
        return $p1 . $p2.$int;
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
