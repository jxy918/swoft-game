<?php declare(strict_types=1);

namespace Swoft\Aop\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\AspectRegister;
use Swoft\Aop\Exception\AopException;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class AspectParser
 *
 * @AnnotationParser(Aspect::class)
 *
 * @since 2.0
 */
class AspectParser extends Parser
{
    /**
     * Parse `Aspect` annotation
     *
     * @param int    $type
     * @param Aspect $annotationObject
     *
     * @return array
     * @throws AopException
     */
    public function parse(int $type, $annotationObject): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AopException('`@Aspect` must be defined by class!');
        }

        AspectRegister::registerAspect($this->className, $annotationObject->getOrder());

        return [$this->className, $this->className, Bean::SINGLETON, ''];
    }
}