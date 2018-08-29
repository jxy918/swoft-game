<?php

namespace Swoft\Bean;

use Swoft\Aop\Aop;
use Swoft\Aop\AopInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\ObjectDefinition\ArgsInjection;
use Swoft\Bean\ObjectDefinition\MethodInjection;
use Swoft\Bean\ObjectDefinition\PropertyInjection;
use Swoft\Bean\Resource\DefinitionResource;
use Swoft\Bean\Resource\ServerAnnotationResource;
use Swoft\Bean\Resource\WorkerAnnotationResource;
use Swoft\Proxy\Handler\AopHandler;
use Swoft\Proxy\Proxy;

/**
 * 全局容器
 *
 * @uses      Container
 * @version   2017年08月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Container
{
    /**
     * Map of entries with Singleton scope that are already resolved.
     *
     * @var array
     */
    private $singletonEntries = [];

    /**
     * 已解析的bean规则
     *
     * @var ObjectDefinition[][]
     */
    private $definitions = [];

    /**
     * properties.php配置信息
     *
     * @var array
     */
    private $properties = [];

    /**
     * 默认创建bean执行的初始化方法
     *
     * @var string
     */
    private $initMethod = 'init';

    /**
     * 获取一个bean
     *
     * @param string $name 名称
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    public function get(string $name)
    {
        // 已经创建
        if (isset($this->singletonEntries[$name])) {
            return $this->singletonEntries[$name];
        }

        // 未定义
        if (!isset($this->definitions[$name])) {
            throw new \InvalidArgumentException(sprintf('Bean %s not exist', $name));
        }

        /* @var ObjectDefinition $objectDefinition */
        $objectDefinition = $this->definitions[$name];

        return $this->set($name, $objectDefinition);
    }

    /**
     * 是否存在某个bean
     *
     * @param string $beanName 名称
     *
     * @return bool
     */
    public function hasBean(string $beanName): bool
    {
        return isset($this->definitions[$beanName]);
    }

    /**
     * 定义配置bean
     *
     * @param array $definitions
     */
    public function addDefinitions(array $definitions)
    {
        $resource          = new DefinitionResource($definitions);
        $this->definitions = array_merge($resource->getDefinitions(), $this->definitions);
    }

    /**
     * Register the annotation of server
     */
    public function autoloadServerAnnotation()
    {
        $bootScan = $this->getScanNamespaceFromProperties('bootScan');
        $resource = new ServerAnnotationResource($this->properties);
        $resource->addScanNamespace($bootScan);
        $definitions = $resource->getDefinitions();

        $this->definitions = array_merge($definitions, $this->definitions);
    }

    /**
     * Register the annotation of worker
     */
    public function autoloadWorkerAnnotation()
    {
        $beanScan = $this->getBeanScanNamespace();
        $resource = new WorkerAnnotationResource($this->properties);
        $resource->addScanNamespace($beanScan);
        $definitions = $resource->getDefinitions();

        $this->definitions = \array_merge($definitions, $this->definitions);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function initBeans()
    {
        $autoInitBeans = $this->properties['autoInitBean'] ?? false;
        if (!$autoInitBeans) {
            return;
        }

        // 循环初始化
        foreach ($this->definitions as $beanName => $definition) {
            $this->get($beanName);
        }
    }

    /**
     * 所有bean定义
     *
     * @return array
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getBeanNames(): array
    {
        return \array_keys($this->definitions);
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * 创建bean
     *
     * @param string           $name             名称
     * @param ObjectDefinition $objectDefinition bean定义
     *
     * @return object
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    private function set(string $name, ObjectDefinition $objectDefinition)
    {
        // bean创建信息
        $scope             = $objectDefinition->getScope();
        $className         = $objectDefinition->getClassName();
        $propertyInjects   = $objectDefinition->getPropertyInjections();
        $constructorInject = $objectDefinition->getConstructorInjection();

        if ($refBeanName = $objectDefinition->getRef()) {
            return $this->get($refBeanName);
        }

        // 构造函数
        $constructorParameters = [];
        if ($constructorInject !== null) {
            $constructorParameters = $this->injectConstructor($constructorInject);
        }

        $reflectionClass = new \ReflectionClass($className);
        $properties      = $reflectionClass->getProperties();

        // new实例
        $isExeMethod = $reflectionClass->hasMethod($this->initMethod);
        $object      = $this->newBeanInstance($reflectionClass, $constructorParameters);

        // 属性注入
        $this->injectProperties($object, $properties, $propertyInjects);

        // 执行初始化方法
        if ($isExeMethod) {
            $object->{$this->initMethod}();
        }

        if (!$object instanceof AopInterface) {
            $object = $this->proxyBean($name, $className, $object);
        }

        // 单例处理
        if ($scope === Scope::SINGLETON) {
            $this->singletonEntries[$name] = $object;
        }

        return $object;
    }

    /**
     * proxy bean
     *
     * @param string $name
     * @param string $className
     * @param object $object
     *
     * @return object
     * @throws \ReflectionException
     */
    private function proxyBean(string $name, string $className, $object)
    {
        /* @var Aop $aop */
        $aop = App::getBean(Aop::class);

        $rc  = new \ReflectionClass($className);
        $rms = $rc->getMethods();
        foreach ($rms as $rm) {
            $method      = $rm->getName();
            $annotations = Collector::$methodAnnotations[$className][$method] ?? [];
            $annotations = array_unique($annotations);
            $aop->match($name, $className, $method, $annotations);
        }

        $handler = new AopHandler($object);

        return Proxy::newProxyInstance(\get_class($object), $handler);
    }

    /**
     * 获取构造函数参数
     *
     * @param MethodInjection $constructorInject
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function injectConstructor(MethodInjection $constructorInject): array
    {
        $constructorParameters = [];

        /* @var ArgsInjection $parameter */
        foreach ($constructorInject->getParameters() as $parameter) {
            $argValue = $parameter->getValue();
            if (\is_array($argValue)) {
                $constructorParameters[] = $this->injectArrayArgs($argValue);
                continue;
            }
            if ($parameter->isRef()) {
                $constructorParameters[] = $this->get($parameter->getValue());
                continue;
            }
            $constructorParameters[] = $parameter->getValue();
        }

        return $constructorParameters;
    }

    /**
     *  初始化Bean实例
     *
     * @param \ReflectionClass $reflectionClass
     * @param array            $constructorParameters
     *
     * @return object
     */
    private function newBeanInstance(\ReflectionClass $reflectionClass, array $constructorParameters)
    {
        if ($reflectionClass->hasMethod('__construct')) {
            return $reflectionClass->newInstanceArgs($constructorParameters);
        }

        return $reflectionClass->newInstance();
    }

    /**
     * 注入属性
     *
     * @param  mixed                $object
     * @param \ReflectionProperty[] $properties $properties
     * @param  mixed                $propertyInjects
     *
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function injectProperties($object, array $properties, $propertyInjects)
    {
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $propertyName = $property->getName();
            if (!isset($propertyInjects[$propertyName])) {
                continue;
            }

            // 设置可用
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }

            /* @var PropertyInjection $propertyInject */
            $propertyInject = $propertyInjects[$propertyName];
            $injectProperty = $propertyInject->getValue();

            // 属性是数组
            if (\is_array($injectProperty)) {
                $injectProperty = $this->injectArrayArgs($injectProperty);
            }

            // 属性是bean引用
            if ($propertyInject->isRef()) {
                $injectProperty = $this->get($injectProperty);
            }

            if ($injectProperty !== null) {
                $property->setValue($object, $injectProperty);
            }
        }
    }

    /**
     * 数组属性值注入
     *
     * @param array $injectProperty
     *
     * @return array
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function injectArrayArgs(array $injectProperty): array
    {
        $injectAry = [];
        foreach ($injectProperty as $key => $property) {
            // 递归循环注入
            if (\is_array($property)) {
                $injectAry[$key] = $this->injectArrayArgs($property);
                continue;
            }

            // 参数注入
            if ($property instanceof ArgsInjection) {
                $propertyValue = $property->getValue();
                if ($property->isRef()) {
                    $injectAry[$key] = $this->get($propertyValue);
                    continue;
                }
                $injectAry[$key] = $propertyValue;
            }
        }

        if (empty($injectAry)) {
            $injectAry = $injectProperty;
        }

        return $injectAry;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    private function getScanNamespaceFromProperties(string $name)
    {
        $properties = $this->properties;

        if (!isset($properties[$name]) || !\is_array($properties[$name])) {
            return [];
        }

        return $properties[$name];
    }

    /**
     * @return array
     */
    private function getBeanScanNamespace(): array
    {
        $beanScan    = $this->getScanNamespaceFromProperties('beanScan');
        $excludeScan = $this->getScanNamespaceFromProperties('excludeScan');
        if (!empty($beanScan)) {
            return array_diff($beanScan, $excludeScan);
        }

        $appDir = alias("@app");
        $dirs   = glob($appDir . "/*");

        $beanNamespace = [];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }
            $nsName          = basename($dir);
            $beanNamespace[] = sprintf('App\%s', $nsName);
        }

        $bootScan = $this->getScanNamespaceFromProperties('bootScan');
        $beanScan = array_diff($beanNamespace, $bootScan, $excludeScan);

        return $beanScan;
    }
}
