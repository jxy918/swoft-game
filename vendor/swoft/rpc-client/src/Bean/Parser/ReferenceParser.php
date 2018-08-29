<?php

namespace Swoft\Rpc\Client\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Rpc\Client\Bean\Annotation\Reference;
use Swoft\Rpc\Client\Bean\Collector\ReferenceCollector;

/**
 * The parser of reference
 */
class ReferenceParser extends AbstractParser
{
    /**
     * @param string    $className
     * @param Reference $objectAnnotation
     * @param string    $propertyName
     * @param string    $methodName
     * @param null      $propertyValue
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        $referenceClass = ReferenceCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return [$referenceClass, true];
    }
}