<?php declare(strict_types=1);


namespace Swoft\Breaker\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Breaker\Annotation\Mapping\Breaker;
use Swoft\Breaker\BreakerRegister;
use Swoft\Breaker\Exception\BreakerException;

/**
 * Class BreakerParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Breaker::class)
 */
class BreakerParser extends Parser
{
    /**
     * @param int     $type
     * @param Breaker $annotationObject
     *
     * @return array
     * @throws BreakerException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type != self::TYPE_METHOD) {
            return [];
        }

        BreakerRegister::registerBreaker($this->className, $this->methodName, $annotationObject);
        return [];
    }
}