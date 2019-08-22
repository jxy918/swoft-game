<?php declare(strict_types=1);


namespace Swoft\Http\Server\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\Middlewares;
use Swoft\Http\Server\Middleware\MiddlewareRegister;

/**
 * Class MiddlewaresParser
 *
 * @AnnotationParser(Middlewares::class)
 *
 * @since 2.0
 */
class MiddlewaresParser extends Parser
{
    /**
     * Parse middlewares
     *
     * @param int         $type
     * @param Middlewares $annotationObject
     *
     * @return array
     */
    public function parse(int $type, $annotationObject): array
    {
        $middlewares = $annotationObject->getMiddlewares();

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                continue;
            }

            $name = $middleware->getName();
            if ($type === self::TYPE_CLASS) {
                MiddlewareRegister::registerByClassName($name, $this->className);
                continue;
            }

            if ($type === self::TYPE_METHOD) {
                MiddlewareRegister::registerByMethodName($name, $this->className, $this->methodName);
            }
        }

        return [];
    }
}