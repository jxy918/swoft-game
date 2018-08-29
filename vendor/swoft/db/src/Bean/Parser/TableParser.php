<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Bean\Collector\EntityCollector;

/**
 * Table注解解析器
 *
 * @uses      TableParser
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class TableParser extends AbstractParser
{
    /**
     * Table注解解析
     *
     * @param string $className
     * @param Table  $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = 'F',
        string $methodName = '',
        $propertyValue = null
    ) {
        EntityCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
