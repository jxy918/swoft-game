<?php

namespace Swoft\Sg\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Sg\Bean\Annotation\Breaker;
use Swoft\Bean\Annotation\Scope;
use Swoft\Sg\Bean\Collector\BreakerCollector;

/**
 * breaker parser
 */
class BreakerParser extends AbstractParser
{
    /**
     * @param string  $className
     * @param Breaker $objectAnnotation
     * @param string  $propertyName
     * @param string  $methodName
     * @param null    $propertyValue
     *
     * @return array
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        BreakerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$className, Scope::SINGLETON, ""];
    }
}
