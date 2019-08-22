<?php declare(strict_types=1);


namespace Swoft\Validator\Annotation\Parser;


use ReflectionException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\ValidatorRegister;
use Swoft\Validator\Annotation\Mapping\Pattern;

/**
 * Class PatternParser
 *
 * @since 2.0
 *
 * @AnnotationParser(annotation=Pattern::class)
 */
class PatternParser extends Parser
{
    /**
     * @param int    $type
     * @param object $annotationObject
     *
     * @return array
     * @throws ReflectionException
     * @throws ValidatorException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type != self::TYPE_PROPERTY) {
            return [];
        }

        ValidatorRegister::registerValidatorItem($this->className, $this->propertyName, $annotationObject);

        return [];
    }
}