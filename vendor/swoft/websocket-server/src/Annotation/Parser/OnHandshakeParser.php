<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Server\SwooleEvent;
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandshake;
use Swoft\WebSocket\Server\Router\RouteRegister;

/**
 * Class OnHandshakeParser
 *
 * @since 2.0
 * @AnnotationParser(OnHandshake::class)
 */
class OnHandshakeParser extends Parser
{
    /**
     * Parse object
     *
     * @param int         $type       Class or Method or Property
     * @param OnHandshake $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_METHOD) {
            throw new AnnotationException('`@OnHandshake` must be defined on class method!');
        }

        RouteRegister::bindEvent($this->className, $this->methodName, SwooleEvent::HANDSHAKE);

        return [];
    }
}
