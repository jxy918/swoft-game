<?php declare(strict_types=1);


namespace Swoft\Http\Server\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Middleware\MiddlewareRegister;

/**
 * Class MiddlewareParser
 *
 * @AnnotationParser(Middleware::class)
 *
 * @since 2.0
 */
class MiddlewareParser extends Parser
{
    /**
     * Parse middleware
     *
     * @param int        $type
     * @param Middleware $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        $name = $annotationObject->getName();

        if ($type === self::TYPE_CLASS) {
            MiddlewareRegister::registerByClassName($name, $this->className);
            return [];
        }

        if ($type === self::TYPE_METHOD) {
            MiddlewareRegister::registerByMethodName($name, $this->className, $this->methodName);
            return [];
        }

        return [];
    }
}