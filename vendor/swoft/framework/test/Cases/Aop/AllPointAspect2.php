<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Aop;

use Swoft\Aop\JoinPoint;
use Swoft\Aop\ProceedingJoinPoint;
use Swoft\Bean\Annotation\After;
use Swoft\Bean\Annotation\AfterReturning;
use Swoft\Bean\Annotation\AfterThrowing;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\Before;
use Swoft\Bean\Annotation\PointBean;

/**
 *
 * @Aspect
 * @PointBean(
 *     include={AopBean::class},
 * )
 *
 * @uses      AllPointAspect2
 * @version   2017年12月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AllPointAspect2
{
    /**
     * @var
     */
    private $test;

    /**
     * @Before
     */
    public function before()
    {
        $this->test .= ' before2 ';
    }

    /**
     * @After
     */
    public function after()
    {
        $this->test .= ' after2 ';
    }

    /**
     * @AfterReturning
     */
    public function afterReturn(JoinPoint $joinPoint)
    {
        return $joinPoint->getReturn().' afterReturn2 ';
    }

    /**
     * @Around
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $this->test .= ' around-before2 ';
        $result = $proceedingJoinPoint->proceed();
        $this->test .= ' around-after2 ';
        return $result.$this->test;
    }

    /**
     * @AfterThrowing
     */
    public function afterThrowing()
    {
        echo "aop=2 afterThrowing !\n";
    }
}
