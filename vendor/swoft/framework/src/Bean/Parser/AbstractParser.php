<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Resource\AnnotationResource;

/**
 * 抽象解析器
 *
 * @uses      AbstractParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * 注解解析资源
     *
     * @var AnnotationResource
     */
    protected $annotationResource;

    /**
     * AbstractParser constructor.
     *
     * @param AnnotationResource $annotationResource
     */
    public function __construct(AnnotationResource $annotationResource)
    {
        $this->annotationResource = $annotationResource;
    }
}
