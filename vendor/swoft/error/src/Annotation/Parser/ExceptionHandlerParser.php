<?php declare(strict_types=1);

namespace Swoft\Error\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Error\ErrorRegister;

/**
 * Class ExceptionHandlerParser
 *
 * @since 2.0
 * @AnnotationParser(ExceptionHandler::class)
 */
class ExceptionHandlerParser extends Parser
{
    /**
     * Parse object
     *
     * @param int              $type       Class or Method or Property
     * @param ExceptionHandler $annotation Annotation object
     *
     * @return array
     *
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@ExceptionHandler` must be defined on class!');
        }

        $handlerClass = $this->className;

        ErrorRegister::add($handlerClass, $annotation->getExceptions());

        return [$handlerClass, $handlerClass, Bean::SINGLETON, ''];
    }
}
