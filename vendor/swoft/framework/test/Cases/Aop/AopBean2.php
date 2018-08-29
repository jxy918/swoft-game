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
 * @uses      AopBean2
 * @version   2018年03月27日
 * @author    maijiankang <maijiankang@foxmail.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AopBean2
{
    public function doAop()
    {
        echo 'do aop';
    }
    
    public function throwSth(\Throwable $t)
    {
        throw $t;
    }
}
