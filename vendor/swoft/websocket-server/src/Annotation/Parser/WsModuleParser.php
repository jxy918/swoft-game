<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\Str;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoft\WebSocket\Server\MessageParser\RawTextParser;
use Swoft\WebSocket\Server\Router\RouteRegister;

/**
 * Class WebSocketParser
 *
 * @since 2.0
 *
 * @AnnotationParser(WsModule::class)
 */
class WsModuleParser extends Parser
{
    /**
     * Parse object
     *
     * @param int      $type Class or Method or Property
     * @param WsModule $ann  Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     * @throws AnnotationException
     */
    public function parse(int $type, $ann): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@WsModule` must be defined on class!');
        }

        $class = $this->className;

        RouteRegister::bindModule($class, [
            'path'           => $ann->getPath() ?: Str::getClassName($class, 'Module'),
            'name'           => $ann->getName(),
            'params'         => $ann->getParams(),
            'class'          => $class,
            'eventMethods'   => [],
            'controllers'    => $ann->getControllers(),
            'messageParser'  => $ann->getMessageParser() ?: RawTextParser::class,
            'defaultOpcode' => $ann->getDefaultOpcode(),
            'defaultCommand' => $ann->getDefaultCommand(),
        ]);

        return [$class, $class, Bean::SINGLETON, ''];
    }
}
