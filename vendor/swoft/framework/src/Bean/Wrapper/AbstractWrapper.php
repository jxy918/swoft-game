<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\ObjectDefinition;
use Swoft\Bean\ObjectDefinition\PropertyInjection;
use Swoft\Bean\Parser\AbstractParser;
use Swoft\Bean\Parser\MethodWithoutAnnotationParser;
use Swoft\Bean\Resource\AnnotationResource;
use Swoft\Bean\Wrapper\Extend\WrapperExtendInterface;

/**
 * 抽象封装器
 */
abstract class AbstractWrapper implements WrapperInterface
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations = [];

    /**
     * 属性注解
     *
     * @var array
     */
    protected $propertyAnnotations = [];

    /**
     * 方法注解
     *
     * @var array
     */
    protected $methodAnnotations = [];

    /**
     * @var WrapperExtendInterface[]
     */
    private $extends = [];


    /**
     * 注解资源
     *
     * @var AnnotationResource
     */
    protected $annotationResource;

    /**
     * AbstractWrapper constructor.
     *
     * @param AnnotationResource $annotationResource
     */
    public function __construct(AnnotationResource $annotationResource)
    {
        $this->annotationResource = $annotationResource;
    }

    /**
     * 封装注解
     *
     * @param string $className
     * @param array  $annotations
     *
     * @return array|null
     */
    public function doWrapper(string $className, array $annotations)
    {
        $reflectionClass = new \ReflectionClass($className);

        // 解析类级别的注解
        $beanDefinition = $this->parseClassAnnotations($className, $annotations['class']);

        // 没配置注入bean注解
        if (empty($beanDefinition) && !$reflectionClass->isInterface()) {
            // 解析属性
            $properties = $reflectionClass->getProperties();

            // 解析属性
            $propertyAnnotations = $annotations['property']??[];
            $this->parseProperties($propertyAnnotations, $properties, $className);

            // 解析方法
            $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            $methodAnnotations = $annotations['method'] ??[];

            $this->parseMethods($methodAnnotations, $className, $publicMethods);

            return null;
        }


        // parser bean annotation
        list($beanName, $scope, $ref) = $beanDefinition;

        // 初始化对象
        $objectDefinition = new ObjectDefinition();
        $objectDefinition->setName($beanName);
        $objectDefinition->setClassName($className);
        $objectDefinition->setScope($scope);
        $objectDefinition->setRef($ref);

        if (!$reflectionClass->isInterface()) {
            // 解析属性
            $properties = $reflectionClass->getProperties();

            // 解析属性
            $propertyAnnotations = $annotations['property']??[];
            $propertyInjections = $this->parseProperties($propertyAnnotations, $properties, $className);
            $objectDefinition->setPropertyInjections($propertyInjections);
        }

        // 解析方法
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methodAnnotations = $annotations['method'] ??[];
        $this->parseMethods($methodAnnotations, $className, $publicMethods);

        return [$beanName, $objectDefinition];
    }

    /**
     * 解析属性
     *
     * @param array  $propertyAnnotations
     * @param array  $properties
     * @param string $className
     *
     * @return array
     */
    private function parseProperties(array $propertyAnnotations, array $properties, string $className)
    {
        $propertyInjections = [];

        /* @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $propertyName = $property->getName();
            if (!isset($propertyAnnotations[$propertyName]) || !$this->isParseProperty($propertyAnnotations[$propertyName])) {
                continue;
            }

            $object = new $className();
            $property->setAccessible(true);
            $propertyValue = $property->getValue($object);

            list($injectProperty, $isRef) = $this->parsePropertyAnnotations($propertyAnnotations, $className, $propertyName, $propertyValue);
            if ($injectProperty == null) {
                continue;
            }

            $propertyInjection = new PropertyInjection($propertyName, $injectProperty, (bool)$isRef);
            $propertyInjections[$propertyName] = $propertyInjection;
        }

        return $propertyInjections;
    }

    /**
     * 解析方法
     *
     * @param array  $methodAnnotations
     * @param string $className
     * @param array  $publicMethods
     */
    private function parseMethods(array $methodAnnotations, string $className, array $publicMethods)
    {
        // 循环解析
        foreach ($publicMethods as $method) {
            /* @var \ReflectionMethod $method*/
            if ($method->isStatic()) {
                continue;
            }

            /* @var \ReflectionClass $declaredClass*/
            $declaredClass = $method->getDeclaringClass();
            $declaredName = $declaredClass->getName();

            // 不是当前类方法
            if ($declaredName != $className) {
                continue;
            }
            $this->parseMethodAnnotations($className, $method, $methodAnnotations);
        }
    }

    /**
     * 解析方法注解
     *
     * @param string            $className
     * @param \ReflectionMethod $method
     * @param array             $methodAnnotations
     */
    private function parseMethodAnnotations(string $className, \ReflectionMethod $method, array $methodAnnotations)
    {
        // 方法没有注解解析
        $methodName = $method->getName();
        $isWithoutMethodAnnotation = empty($methodAnnotations) || !isset($methodAnnotations[$methodName]);
        if ($isWithoutMethodAnnotation || !$this->isParseMethod($methodAnnotations[$methodName])) {
            $this->parseMethodWithoutAnnotation($className, $methodName);
            return;
        }

        // 循环方法注解解析
        foreach ($methodAnnotations[$methodName] as $methodAnnotationAry) {
            foreach ($methodAnnotationAry as $methodAnnotation) {
                $annotationClass = get_class($methodAnnotation);
                if (!in_array($annotationClass, $this->getMethodAnnotations())) {
                    continue;
                }

                // 解析器解析
                $annotationParser = $this->getAnnotationParser($methodAnnotation);
                if ($annotationParser == null) {
                    $this->parseMethodWithoutAnnotation($className, $methodName);
                    continue;
                }
                $annotationParser->parser($className, $methodAnnotation, "", $methodName);
            }
        }
    }

    /**
     * 方法没有配置路由注解解析
     *
     * @param string $className
     * @param string $methodName
     */
    private function parseMethodWithoutAnnotation(string $className, string $methodName)
    {
        $parser = new MethodWithoutAnnotationParser($this->annotationResource);
        $parser->parser($className, null, "", $methodName);
    }

    /**
     * 属性解析
     *
     * @param  array $propertyAnnotations
     * @param string $className
     * @param string $propertyName
     * @param mixed  $propertyValue
     *
     * @return array
     */
    private function parsePropertyAnnotations(array $propertyAnnotations, string $className, string $propertyName, $propertyValue)
    {
        $isRef = false;
        $injectProperty = "";

        // 没有任何注解
        if (empty($propertyAnnotations) || !isset($propertyAnnotations[$propertyName])
            || !$this->isParseProperty($propertyAnnotations[$propertyName])
        ) {
            return [null, false];
        }

        // 属性注解解析
        foreach ($propertyAnnotations[$propertyName] as $propertyAnnotation) {
            $annotationClass = get_class($propertyAnnotation);
            if (!in_array($annotationClass, $this->getPropertyAnnotations())) {
                continue;
            }

            // 解析器
            $annotationParser = $this->getAnnotationParser($propertyAnnotation);
            if ($annotationParser === null) {
                $injectProperty = null;
                $isRef = false;
                continue;
            }
            list($injectProperty, $isRef) = $annotationParser->parser($className, $propertyAnnotation, $propertyName, "", $propertyValue);
        }

        return [$injectProperty, $isRef];
    }

    /**
     * 类注解解析
     *
     * @param string $className
     * @param array  $annotations
     *
     * @return array
     */
    public function parseClassAnnotations(string $className, array $annotations)
    {
        if (!$this->isParseClass($annotations)) {
            return null;
        }

        $beanData = null;
        foreach ($annotations as $annotation) {
            $annotationClass = get_class($annotation);
            if (!in_array($annotationClass, $this->getClassAnnotations())) {
                continue;
            }

            // annotation parser
            $annotationParser = $this->getAnnotationParser($annotation);
            if ($annotationParser == null) {
                continue;
            }
            $annotationData = $annotationParser->parser($className, $annotation);
            if ($annotationData != null) {
                $beanData = $annotationData;
            }
        }

        return $beanData;
    }

    /**
     * @param WrapperExtendInterface $extend
     */
    public function addExtends(WrapperExtendInterface $extend)
    {
        $extendClass = get_class($extend);
        $this->extends[$extendClass] = $extend;
    }

    /**
     * @return array
     */
    private function getClassAnnotations(): array
    {
        return array_merge($this->classAnnotations, $this->getExtendAnnotations(1));
    }

    /**
     * @return array
     */
    private function getPropertyAnnotations(): array
    {
        return array_merge($this->propertyAnnotations, $this->getExtendAnnotations(2));
    }

    /**
     * @return array
     */
    private function getMethodAnnotations(): array
    {
        return array_merge($this->methodAnnotations, $this->getExtendAnnotations(3));
    }

    /**
     * @param int $type
     *
     * @return array
     */
    private function getExtendAnnotations(int $type = 1): array
    {
        $annotations = [];
        foreach ($this->extends as $extend) {
            if ($type == 1) {
                $extendAnnoation = $extend->getClassAnnotations();
            } elseif ($type == 2) {
                $extendAnnoation = $extend->getPropertyAnnotations();
            } else {
                $extendAnnoation = $extend->getMethodAnnotations();
            }
            $annotations = array_merge($annotations, $extendAnnoation);
        }

        return $annotations;
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    private function isParseClass(array $annotations): bool
    {
        return $this->isParseClassAnnotations($annotations) || $this->isParseExtendAnnotations($annotations, 1);
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    private function isParseProperty(array $annotations): bool
    {
        return $this->isParsePropertyAnnotations($annotations) || $this->isParseExtendAnnotations($annotations, 2);
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    private function isParseMethod(array $annotations): bool
    {
        return $this->isParseMethodAnnotations($annotations) || $this->isParseExtendAnnotations($annotations, 3);
    }

    /**
     * @param array $annotations
     * @param int   $type
     *
     * @return bool
     */
    private function isParseExtendAnnotations(array $annotations, int $type = 1): bool
    {
        foreach ($this->extends as $extend) {
            if ($type == 1) {
                $isParse = $extend->isParseClassAnnotations($annotations);
            } elseif ($type == 2) {
                $isParse = $extend->isParsePropertyAnnotations($annotations);
            } else {
                $isParse = $extend->isParseMethodAnnotations($annotations);
            }
            if ($isParse) {
                return true;
            }
        }

        return false;
    }

    /**
     *  获取注解对应解析器
     *
     * @param $objectAnnotation
     *
     * @return AbstractParser
     */
    private function getAnnotationParser($objectAnnotation)
    {
        $annotationClassName = get_class($objectAnnotation);
        $classNameTmp = str_replace('\\', '/', $annotationClassName);
        $className = basename($classNameTmp);
        $namespaceDir = dirname($classNameTmp, 2);
        $namespace = str_replace('/', '\\', $namespaceDir);

        // 解析器类名
        $annotationParserClassName = "{$namespace}\\Parser\\{$className}Parser";
        if (!class_exists($annotationParserClassName)) {
            return null;
        }

        $annotationParser = new $annotationParserClassName($this->annotationResource);
        return $annotationParser;
    }
}
