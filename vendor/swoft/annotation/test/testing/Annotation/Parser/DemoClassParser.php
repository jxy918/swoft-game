<?php declare(strict_types=1);


namespace SwoftTest\Annotation\Testing\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use SwoftTest\Annotation\Testing\Annotation\Mapping\DemoClass;

/**
 * Class DemoClassParser
 *
 * @since 2.0
 *
 * @AnnotationParser(annotation=DemoClass::class)
 */
class DemoClassParser extends Parser
{
    /**
     * @param int       $type
     * @param DemoClass $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        return [];
    }
}