<?php declare(strict_types=1);


namespace Swoft\Db\Annotation\Parser;

use function trim;
use function explode;
use function preg_match;
use ReflectionException;
use ReflectionProperty;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\EntityRegister;
use Swoft\Db\Exception\DbException;

/**
 * Class ColumnParser
 *
 * @AnnotationParser(Column::class)
 * @since 2.0
 */
class ColumnParser extends Parser
{
    /**
     * @param int    $type
     * @param Column $annotationObject
     *
     * @return array
     * @throws DbException
     * @throws ReflectionException
     */
    public function parse(int $type, $annotationObject): array
    {
        $type   = $this->getPropertyType();
        $name   = $annotationObject->getName();
        $prop   = $annotationObject->getProp();
        $hidden = $annotationObject->isHidden();
        $name   = empty($name) ? $this->propertyName : $name;
        $prop   = empty($prop) ? $this->propertyName : $prop;

        EntityRegister::registerColumn($this->className, $this->propertyName, $name, $prop, $hidden, $type);
        return [];
    }

    /**
     * Get property `
     *
     * @return string
     * @throws ReflectionException
     */
    private function getPropertyType(): string
    {
        // Parse php document
        $reflectProperty = new ReflectionProperty($this->className, $this->propertyName);
        $document        = $reflectProperty->getDocComment();

        if (!preg_match('/@var\s+([^\s]+)/', $document, $matches)) {
            return '';
        }

        $type = $matches[1] ?? '';
        $type = explode('|', $type);

        return trim($type[0]);
    }
}
