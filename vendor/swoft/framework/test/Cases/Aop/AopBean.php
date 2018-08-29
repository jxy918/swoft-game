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

use Swoft\Bean\Annotation\Bean;

/**
 *
 * @Bean
 * @uses      AopBean
 * @version   2017年12月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopBean
{
    public function doAop()
    {
        return 'do aop';
    }

    public function doAop2()
    {
        return 'do aop';
    }
}
