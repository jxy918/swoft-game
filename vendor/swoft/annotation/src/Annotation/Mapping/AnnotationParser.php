<?php declare(strict_types=1);

namespace Swoft\Annotation\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class AnnotationParser
 *
 * @since 2.0
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("annotation", type="string"),
 * })
 */
final class AnnotationParser
{
    /**
     * @var string
     */
    private $annotation = '';

    /**
     * AnnotationParser constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->annotation = $values['value'];
        }
        if (isset($values['annotation'])) {
            $this->annotation = $values['annotation'];
        }
    }

    /**
     * @return string
     */
    public function getAnnotation(): string
    {
        return $this->annotation;
    }
}