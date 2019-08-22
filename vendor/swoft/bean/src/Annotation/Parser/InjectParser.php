<?php declare(strict_types=1);

namespace Swoft\Bean\Annotation\Parser;

use PhpDocReader\AnnotationException;
use PhpDocReader\PhpDocReader;
use ReflectionException;
use ReflectionProperty;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\BeanException;
use Swoft\Bean\InterfaceRegister;

/**
 * Class InjectParser
 *
 * @AnnotationParser(Inject::class)
 * @since 2.0
 */
class InjectParser extends Parser
{
    /**
     * Parse annotation
     *
     * @param int    $type
     * @param Inject $annotationObject
     *
     * @return array
     * @throws BeanException
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function parse(int $type, $annotationObject): array
    {
        // Only to parse property annotation with `@Inject`
        if ($type != self::TYPE_PROPERTY) {
            return [];
        }

        $inject = $annotationObject->getName();
        if (!empty($inject)) {
            return [$inject, true];
        }

        // Parse php document
        $phpReader       = new PhpDocReader();
        $reflectProperty = new ReflectionProperty($this->className, $this->propertyName);
        $docInject       = $phpReader->getPropertyClass($reflectProperty);
        if (empty($docInject)) {
            throw new BeanException('`@Inejct` must be define inejct value or `@var type` ');
        }

        return [$docInject, true];
    }
}
