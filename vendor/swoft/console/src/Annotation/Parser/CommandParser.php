<?php declare(strict_types=1);

namespace Swoft\Console\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\CommandRegister;
use Swoft\Stdlib\Helper\Str;

/**
 * Class CommandParser
 *
 * @since 2.0
 *
 * @AnnotationParser(Command::class)
 */
class CommandParser extends Parser
{
    /**
     * Parse object
     *
     * @param int     $type       Class or Method or Property
     * @param Command $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     */
    public function parse(int $type, $annotation): array
    {
        if ($type !== self::TYPE_CLASS) {
            throw new AnnotationException('`@Command` must be defined on class!');
        }

        $class = $this->className;
        $group = $annotation->getName() ?: Str::getClassName($class, 'Command');

        // Add group for the command controller
        CommandRegister::addGroup($class, $group, [
            'group'          => $group,
            'desc'           => $annotation->getDesc(),
            'alias'          => $annotation->getAlias(),
            'aliases'        => $annotation->getAliases(),
            'enabled'        => $annotation->isEnabled(),
            'coroutine'      => $annotation->isCoroutine(),
            'idAliases'      => $annotation->getIdAliases(),
            'defaultCommand' => $annotation->getDefaultCommand(),
        ]);

        return [$class, $class, Bean::SINGLETON, ''];
    }
}
