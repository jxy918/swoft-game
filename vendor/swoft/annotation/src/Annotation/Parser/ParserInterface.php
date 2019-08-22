<?php declare(strict_types=1);

namespace Swoft\Annotation\Annotation\Parser;

use ReflectionClass;

/**
 * Interface ParserInterface
 *
 * @since 2.0
 */
interface ParserInterface
{
    /**
     * Parser constructor.
     *
     * @param string           $className
     * @param ReflectionClass $reflectionClass
     * @param array            $classAnnotations
     */
    public function __construct(string $className, ReflectionClass $reflectionClass, array $classAnnotations);

    /**
     * Parse object
     *
     * @param int    $type             Class or Method or Property
     * @param object $annotationObject Annotation object
     *
     * @return array
     * Return empty array is nothing to do!
     * When class type return [$beanName, $className, $scope, $alias] is to inject bean
     * When property type return [$propertyValue, $isRef] is to reference value
     */
    public function parse(int $type, $annotationObject): array;

    /**
     * Get definition config
     *
     * @return array
     */
    public function getDefinitions(): array;

    /**
     * Set method name
     *
     * @param string $methodName
     */
    public function setMethodName(string $methodName): void;

    /**
     * Set property name
     *
     * @param string $propertyName
     */
    public function setPropertyName(string $propertyName): void;
}