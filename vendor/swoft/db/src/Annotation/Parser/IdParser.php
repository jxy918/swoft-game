<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\EntityRegister;
use Swoft\Db\Exception\DbException;

/**
 * Class IdParser
 *
 * @AnnotationParser(Id::class)
 * @since 2.0
 */
class IdParser extends Parser
{
    /**
     * @param int    $type
     * @param object $annotationObject
     *
     * @return array
     * @throws DbException
     */
    public function parse(int $type, $annotationObject): array
    {
        EntityRegister::registerId($this->className, $this->propertyName, $annotationObject->isIncrementing());
        return [];
    }
}