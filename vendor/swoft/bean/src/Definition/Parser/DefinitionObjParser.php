<?php declare(strict_types=1);

namespace Swoft\Bean\Definition\Parser;

use InvalidArgumentException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Definition\ArgsInjection;
use Swoft\Bean\Definition\MethodInjection;
use Swoft\Bean\Definition\ObjectDefinition;
use Swoft\Bean\Definition\PropertyInjection;
use function array_unique;
use function in_array;
use function is_array;
use function is_string;

/**
 * Class DefinitionParser
 *
 * @since 2.0
 */
class DefinitionObjParser extends ObjectParser
{
    /**
     * Parse definitions
     *
     * @return array
     */
    public function parseDefinitions(): array
    {
        foreach ($this->definitions as $beanName => $definition) {
            if (isset($this->objectDefinitions[$beanName])) {
                $objectDefinition = $this->objectDefinitions[$beanName];
                $this->resetObjectDefinition($beanName, $objectDefinition, $definition);
                continue;
            }

            $this->createObjectDefinition($beanName, $definition);
        }

        return [$this->definitions, $this->objectDefinitions, $this->classNames, $this->aliases];
    }

    /**
     * Reset object definition
     *
     * @param string           $beanName
     * @param ObjectDefinition $objDefinition
     * @param array            $definition
     */
    private function resetObjectDefinition(string $beanName, ObjectDefinition $objDefinition, array $definition): void
    {
        // Parse class name
        $className    = $definition['class'] ?? '';
        $objClassName = $objDefinition->getClassName();

        if (!empty($className) && $className !== $objClassName) {
            throw new InvalidArgumentException('Class for annotations and definitions must be the same Or not to define class');
        }

        $objDefinition = $this->updateObjectDefinitionByDefinition($objDefinition, $definition);

        $this->objectDefinitions[$beanName] = $objDefinition;
    }

    /**
     * Create object definition for definition
     *
     * @param string $beanName
     * @param array  $definition
     */
    private function createObjectDefinition(string $beanName, array $definition): void
    {
        $className = $definition['class'] ?? '';
        if (empty($className)) {
            throw new InvalidArgumentException(sprintf('%s key for definition must be defined class', $beanName));
        }

        $objDefinition = new ObjectDefinition($beanName, $className);
        $objDefinition = $this->updateObjectDefinitionByDefinition($objDefinition, $definition);

        $classNames   = $this->classNames[$className] ?? [];
        $classNames[] = $beanName;

        $this->classNames[$className]       = array_unique($classNames);
        $this->objectDefinitions[$beanName] = $objDefinition;
    }

    /**
     * Parse definition
     *
     * @param array $definition
     *
     * @return array
     */
    private function parseDefinition(array $definition): array
    {
        // Remove class key
        unset($definition['class']);

        // Parse construct
        $constructArgs = $definition[0] ?? [];
        if (!is_array($constructArgs)) {
            throw new InvalidArgumentException('Construct args for definition must be array');
        }

        // Parse construct args
        $argInjects = [];
        foreach ($constructArgs as $arg) {
            [$argValue, $argIsRef] = $this->getValueByRef($arg);

            $argInjects[] = new ArgsInjection($argValue, $argIsRef);
        }

        // Set construct inject
        $constructInject = null;
        if (!empty($argInjects)) {
            $constructInject = new MethodInjection('__construct', $argInjects);
        }

        // Remove construct definition
        unset($definition[0]);

        // Parse definition option
        $option = $definition['__option'] ?? [];
        if (!is_array($option)) {
            throw new InvalidArgumentException('__option for definition must be array');
        }

        // Remove `__option`
        unset($definition['__option']);

        // Parse definition properties
        $propertyInjects = [];
        foreach ($definition as $propertyName => $propertyValue) {
            if (!is_string($propertyName)) {
                throw new InvalidArgumentException('Property key from definition must be string');
            }

            [$proValue, $proIsRef] = $this->getValueByRef($propertyValue);

            // Parse property for array
            if (is_array($proValue)) {
                $proValue = $this->parseArrayProperty($proValue);
            }

            $propertyInject = new PropertyInjection($propertyName, $proValue, $proIsRef);

            $propertyInjects[$propertyName] = $propertyInject;
        }

        return [$constructInject, $propertyInjects, $option];
    }

    /**
     * Update definition
     *
     * @param ObjectDefinition $objDfn
     * @param array            $definition
     *
     * @return ObjectDefinition
     */
    private function updateObjectDefinitionByDefinition(ObjectDefinition $objDfn, array $definition): ObjectDefinition
    {
        [$constructInject, $propertyInjects, $option] = $this->parseDefinition($definition);

        // Set construct inject
        if (!empty($constructInject)) {
            $objDfn->setConstructorInjection($constructInject);
        }

        // Set property inject
        foreach ($propertyInjects as $propertyName => $propertyInject) {
            $objDfn->setPropertyInjection($propertyName, $propertyInject);
        }

        $scopes = [
            Bean::SINGLETON,
            Bean::PROTOTYPE,
            Bean::REQUEST,
        ];

        $scope = $option['scope'] ?? '';
        $alias = $option['alias'] ?? '';

        if (!empty($scope) && !in_array($scope, $scopes, true)) {
            throw new InvalidArgumentException('Scope for definition is not undefined');
        }

        // Update scope
        if (!empty($scope)) {
            $objDfn->setScope($scope);
        }

        // Update alias
        if (!empty($alias)) {
            $objDfn->setAlias($alias);

            $objAlias = $objDfn->getAlias();
            unset($this->aliases[$objAlias]);

            $this->aliases[$alias] = $objDfn->getName();
        }

        return $objDfn;
    }

    /**
     * Parse array property
     *
     * @param array $propertyValue
     *
     * @return array
     */
    private function parseArrayProperty(array $propertyValue): array
    {
        foreach ($propertyValue as $proKey => &$proValue) {
            [$refValue, $isRef] = $this->getValueByRef($proValue);
            if (!$isRef) {
                continue;
            }

            $proValue = new ArgsInjection($refValue, $isRef);
        }

        return $propertyValue;
    }
}
