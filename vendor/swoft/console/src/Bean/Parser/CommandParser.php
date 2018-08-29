<?php

namespace Swoft\Console\Bean\Parser;

use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Parser\AbstractParser;
use Swoft\Console\Bean\Annotation\Command;
use Swoft\Console\Bean\Collector\CommandCollector;

/**
 * the parser of command
 *
 * @uses      CommandParser
 * @version   2018年01月22日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CommandParser extends AbstractParser
{
    /**
     * @param string $className
     * @param Command $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     *
     * @param null $propertyValue
     * @return mixed
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;

        CommandCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
