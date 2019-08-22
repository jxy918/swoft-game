<?php

namespace Swoft\Http\Server\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Exception\HttpServerException;
use Swoft\Http\Server\Router\RouteRegister;

/**
 * Class ControllerParser
 *
 * @AnnotationParser(Controller::class)
 *
 * @since 2.0
 */
class ControllerParser extends Parser
{
    /**
     * @param int        $type
     * @param Controller $annotation
     *
     * @return array
     * @throws HttpServerException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new HttpServerException('`@Controller` must be defined by class!');
        }

        // add route prefix for controller
        RouteRegister::addPrefix($this->className, $annotation->getPrefix());

        return [$this->className, $this->className, Bean::SINGLETON, ''];
    }
}