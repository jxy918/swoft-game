<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\EntityRegister;

/**
 * Class EntityParser
 *
 * @AnnotationParser(Entity::class)
 * @since 2.0
 */
class EntityParser extends Parser
{
    /**
     * @param int    $type
     * @param Entity $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        EntityRegister::registerEntity($this->className, $annotationObject->getTable(), $annotationObject->getPool());

        return [$this->className, $this->className, Bean::PROTOTYPE, ''];
    }
}