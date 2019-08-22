<?php declare(strict_types=1);

namespace Swoft\Bean\Annotation\Parser;

use ReflectionClass;
use ReflectionException;
use Swoft\Annotation\Annotation\Mapping\AnnotationParser;
use Swoft\Annotation\Annotation\Parser\Parser;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\InterfaceRegister;

/**
 * Class BeanParser
 *
 * @AnnotationParser(Bean::class)
 *
 * @since 2.0
 */
class BeanParser extends Parser
{
    /**
     * Parse object
     *
     * @param int  $type
     * @param Bean $annotationObject
     *
     * @return array
     * @throws ReflectionException
     */
    public function parse(int $type, $annotationObject): array
    {
        // Only to parse class annotation with `@Bean`
        if ($type != self::TYPE_CLASS) {
            return [];
        }

        $name  = $annotationObject->getName();
        $scope = $annotationObject->getScope();
        $alias = $annotationObject->getAlias();

        $this->registerInterface($name);

        return [$name, $this->className, $scope, $alias];
    }

    /**
     * @param string $beanName
     *
     * @throws ReflectionException
     */
    private function registerInterface(string $beanName): void
    {
        $rc = new ReflectionClass($this->className);

        $interfaces = $rc->getInterfaces();
        if (empty($interfaces)) {
            return;
        }

        foreach ($interfaces as $interface) {
            InterfaceRegister::registerInterface($interface->getName(), $this->className, $beanName);
        }
    }
}