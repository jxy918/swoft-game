<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Annotation\Parser;


use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Rpc\Server\Annotation\Mapping\Middleware;
use Swoft\Rpc\Server\Annotation\Mapping\Middlewares;
use Swoft\Rpc\Server\Middleware\MiddlewareRegister;

/**
 * Class MiddlewaresParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Middlewares::class)
 */
class MiddlewaresParser extends Parser
{
    /**
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