<?php

namespace Swoft\Http\Server\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Bean\Annotation\Scope;
use Swoft\Http\Server\Bean\Collector\ControllerCollector;

/**
 * Controller parser
 */
class ControllerParser extends AbstractParser
{
    /**
     * Parse @Controller annotation
     *
     * @param string      $className
     * @param Controller  $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null): array
    {
        $beanName = $className;
        $scope = Scope::SINGLETON;

        // collect controller
        ControllerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
