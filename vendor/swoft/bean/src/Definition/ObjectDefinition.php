<?php declare(strict_types=1);

namespace Swoft\Bean\Definition;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class ObjectDefinition
 *
 * @since 2.0
 */
class ObjectDefinition
{
    /**
     * Entry name (most of the time, same as $classname).
     *
     * @var string
     */
    private $name;

    /**
     * Class name (if null, then the class name is $name).
     *
     * @var string
     */
    private $className;

    /**
     * Bean scope
     *
     * @var string
     */
    private $scope = Bean::SINGLETON;

    /**
     * @var string
     */
    private $alias = '';

    /**
     * Constructor parameter injection.
     *
     * @var MethodInjection
     */
    private $constructorInjection;

    /**
     * Property injections.
     *
     * @var PropertyInjection[]
     * @example
     * [
     *     'propertyName' => new PropertyInjection,
     *     'propertyName' => new PropertyInjection,
     *     'propertyName' => new PropertyInjection,
     * ]
     */
    private $propertyInjections = [];

    /**
     * Method calls.
     *
     * @var MethodInjection[]
     * @example
     * [
     *     'methodName' => new MethodInjection,
     *     'methodName' => new MethodInjection,
     *     'methodName' => new MethodInjection,
     * ]
     */
    private $methodInjections = [];

    /**
     * ObjectDefinition constructor.
     *
     * @param string $name
     * @param string $className
     * @param string $scope
     * @param string $alias
     */
    public function __construct(
        string $name,
        string $className,
        string $scope = Bean::SINGLETON,
        string $alias = ''
    ) {
        $this->name      = $name;
        $this->scope     = $scope;
        $this->alias     = $alias;
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return MethodInjection
     */
    public function getConstructorInjection(): ?MethodInjection
    {
        return $this->constructorInjection;
    }

    /**
     * @return PropertyInjection[]
     */
    public function getPropertyInjections(): array
    {
        return $this->propertyInjections;
    }

    /**
     * @return MethodInjection[]
     */
    public function getMethodInjections(): array
    {
        return $this->methodInjections;
    }

    /**
     * @param MethodInjection $constructorInjection
     */
    public function setConstructorInjection(MethodInjection $constructorInjection): void
    {
        $this->constructorInjection = $constructorInjection;
    }

    /**
     * @param PropertyInjection[] $propertyInjections
     */
    public function setPropertyInjections(array $propertyInjections): void
    {
        $this->propertyInjections = $propertyInjections;
    }

    /**
     * @param MethodInjection[] $methodInjections
     */
    public function setMethodInjections(array $methodInjections): void
    {
        $this->methodInjections = $methodInjections;
    }

    /**
     * @param string            $propertyName
     * @param PropertyInjection $propertyInjection
     */
    public function setPropertyInjection(string $propertyName, PropertyInjection $propertyInjection): void
    {
        $this->propertyInjections[$propertyName] = $propertyInjection;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }
}