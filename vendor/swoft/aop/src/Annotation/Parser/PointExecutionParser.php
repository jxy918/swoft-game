<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Aop\Annotation\Mapping\PointExecution;
use Swoft\Aop\AspectRegister;
use Swoft\Aop\Exception\AopException;

/**
 * Class PointExecutionParser
 *
 * @AnnotationParser(PointExecution::class)
 *
 * @since 2.0
 */
class PointExecutionParser extends Parser
{
    /**
     * Parse `PointExecution` annotation
     *
     * @param int            $type
     * @param PointExecution $annotationObject
     *
     * @return array
     * @throws AopException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AopException('`@PointExecution` must be defined by class!');
        }

        $include = $annotationObject->getInclude();
        $exclude = $annotationObject->getExclude();

        AspectRegister::registerPoint(AspectRegister::POINT_EXECUTION, $this->className, $include, $exclude);

        return [];
    }
}