<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Aop\Annotation\Mapping\After;
use Swoft\Aop\AspectRegister;
use Swoft\Aop\Exception\AopException;

/**
 * Class AfterParser
 *
 * @AnnotationParser(After::class)
 *
 * @since 2.0
 */
class AfterParser extends Parser
{
    /**
     * Parse `After` annotation
     *
     * @param int   $type
     * @param After $annotationObject
     *
     * @return array
     * @throws AopException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AopException('`@After` must be defined by method!');
        }

        AspectRegister::registerAdvice(AspectRegister::ADVICE_AFTER, $this->className, $this->methodName);

        return [];
    }
}
