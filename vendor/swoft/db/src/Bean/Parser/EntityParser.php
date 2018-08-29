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
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Collector\EntityCollector;

/**
 * EntityParser
 */
class EntityParser extends AbstractParser
{
    /**
     * Entity注解解析
     *
     * @param string $className
     * @param Entity $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return null
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        EntityCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
