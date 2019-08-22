<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Aop\Annotation\Mapping\PointBean;
use Swoft\Aop\AspectRegister;
use Swoft\Aop\Exception\AopException;

/**
 * Class PointBeanParser
 *
 * @AnnotationParser(PointBean::class)
 *
 * @since 2.0
 */
class PointBeanParser extends Parser
{
    /**
     * Parse `PointBean` annotation
     *
     * @param int       $type
     * @param PointBean $annotationObject
     *
     * @return array
     * @throws AopException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AopException('`@PointAnnotation` must be defined by class!');
        }

        $include = $annotationObject->getInclude();
        $exclude = $annotationObject->getExclude();

        AspectRegister::registerPoint(AspectRegister::POINT_BEAN, $this->className, $include, $exclude);

        return [];
    }
}