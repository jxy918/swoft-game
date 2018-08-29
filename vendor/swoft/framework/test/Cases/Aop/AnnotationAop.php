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
use Swoft\Bean\Annotation\Cacheable;
use Swoft\Bean\Annotation\CachePut;

/**
 *
 * @Bean
 * @uses      AnnotationAop
 * @version   2017年12月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AnnotationAop
{
    /**
     * @CachePut
     * @return string
     */
    public function cachePut()
    {
        return 'cachePut';
    }

    /**
     * @Cacheable
     * @return string
     */
    public function cacheable()
    {
        return 'cacheable';
    }
}
