<?php

namespace Swoft\Bean\Annotation;

/**
 * the types of validator
 *
 * @uses      ValidatorFrom
 * @version   2017年12月02日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ValidatorFrom
{
    /**
     * get params
     */
    const GET = 'get';

    /**
     * post params
     */
    const POST = 'post';

    /**
     * path params
     */
    const PATH = 'path';

    /**
     * get/post/path params
     */
    const QUERY = 'query';

    /**
     * the property of entity
     */
    const PROPERTY = 'property';

    /**
     * rpc args
     */
    const SERVICE = 'service';
}
