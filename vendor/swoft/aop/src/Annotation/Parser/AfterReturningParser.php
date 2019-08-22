<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Aop\Annotation\Mapping\AfterReturning;
use Swoft\Aop\AspectRegister;
use Swoft\Aop\Exception\AopException;

/**
 * Class AfterReturningParser
 *
 * @AnnotationParser(AfterReturning::class)
 *
 * @since 2.0
 */
class AfterReturningParser extends Parser
{
    /**
     * Parse `AfterReturning` annotation
     *
     * @param int            $type
     * @param AfterReturning $annotationObject
     *
     * @return array
     * @throws AopException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AopException('`@AfterReturning` must be defined by method!');
        }

        AspectRegister::registerAdvice(AspectRegister::ADVICE_AFTERRETURNING, $this->className, $this->methodName);

        return [];
    }
}