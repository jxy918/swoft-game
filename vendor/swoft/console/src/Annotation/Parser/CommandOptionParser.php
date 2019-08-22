<?php declare(strict_types=1);

namespace Swoft\Console\Annotation\Parser;

use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\CommandRegister;
use Toolkit\Cli\Flags;

/**
 * Class CommandOptionParser
 * @since 2.0
 *
 * @AnnotationParser(CommandOption::class)
 */
class CommandOptionParser extends Parser
{
    /**
     * Parse object
     *
     * @param int           $type       Class or Method or Property
     * @param CommandOption $annotation Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias, $size] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     * @throws AnnotationException
     */
    public function parse(int $type, $annotation): array
    {
        if ($type === self::TYPE_PROPERTY) {
            throw new AnnotationException('`@CommandOption` must be defined on class or method!');
        }

        $method  = $this->methodName;
        $valType = $annotation->getType();
        $defVal  = $annotation->getDefault();

        // Add route info for group command action
        CommandRegister::bindOption($this->className, $method, $annotation->getName(), [
            'method'  => $method,
            'name'    => $annotation->getName(),
            'short'   => $annotation->getShort(),
            'desc'    => $annotation->getDesc(),
            'mode'    => $annotation->getMode(),
            'type'    => $annotation->getType(),
            'default' => $valType === 'BOOL' ? Flags::filterBool($defVal) : $defVal,
        ]);

        return [];
    }
}
