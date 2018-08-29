<?php

namespace Swoft\Task\Bean\Parser;

use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Parser\AbstractParser;
use Swoft\Task\Bean\Annotation\Task;
use Swoft\Task\Bean\Collector\TaskCollector;

/**
 * Task annotation parser
 */
class TaskParser extends AbstractParser
{
    /**
     * @param string $className
     * @param Task   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return array
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        $name = $objectAnnotation->getName();
        $beanName = empty($name) ? $className : $name;

        TaskCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return [$beanName, Scope::SINGLETON, ''];
    }
}
