<?php

namespace Swoft\Bean;

use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Bean\Collector\BootBeanCollector;
use Swoft\Bean\Collector\DefinitionCollector;
use Swoft\Core\Config;
use Swoft\Helper\ArrayHelper;
use Swoft\Helper\DirHelper;

/**
 * Bean Factory
 */
class BeanFactory implements BeanFactoryInterface
{
    /**
     * @var Container Bean container
     */
    private static $container;

    /**
     * Init beans
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public static function init()
    {
        $properties = self::getProperties();

        self::$container = new Container();
        self::$container->setProperties($properties);
        self::$container->autoloadServerAnnotation();

        $definition = self::getServerDefinition();
        self::$container->addDefinitions($definition);
        self::$container->initBeans();
    }

    /**
     * Reload bean definitions
     *
     * @param array $definitions append definitions to config loader
     * @throws \ReflectionException
     */
    public static function reload(array $definitions = [])
    {
        $properties           = self::getProperties();
        $workerDefinitions    = self::getWorkerDefinition();
        $definitions          = ArrayHelper::merge($workerDefinitions, $definitions);

        self::$container->setProperties($properties);
        self::$container->addDefinitions($definitions);
        self::$container->autoloadWorkerAnnotation();

        $componentDefinitions = self::getComponentDefinitions();
        self::$container->addDefinitions($componentDefinitions);

        /* @var Aop $aop Init reload AOP */
        $aop = App::getBean(Aop::class);
        $aop->init();

        self::$container->initBeans();
    }

    /**
     * Get bean from container
     *
     * @param string $name Bean name
     *
     * @return mixed
     */
    public static function getBean(string $name)
    {
        return self::$container->get($name);
    }

    /**
     * Determine if bean exist in container
     *
     * @param string $name Bean name
     *
     * @return bool
     */
    public static function hasBean(string $name): bool
    {
        return self::$container->hasBean($name);
    }

    /**
     * @return array
     */
    private static function getWorkerDefinition(): array
    {
        $configDefinitions = [];
        $beansDir          = App::getAlias('@beans');

        if (\is_readable($beansDir)) {
            $config = new Config();
            $config->load($beansDir, [], DirHelper::SCAN_BFS, Config::STRUCTURE_MERGE);
            $configDefinitions = $config->toArray();
        }

        $coreBeans   = self::getCoreBean(BootBeanCollector::TYPE_WORKER);

        return ArrayHelper::merge($coreBeans, $configDefinitions);
    }

    /**
     * @return array
     * @throws \InvalidArgumentException
     */
    private static function getServerDefinition(): array
    {
        $file             = App::getAlias('@console');
        $configDefinition = [];

        if (\is_readable($file)) {
            $configDefinition = require_once $file;
        }

        $coreBeans  = self::getCoreBean(BootBeanCollector::TYPE_SERVER);

        return ArrayHelper::merge($coreBeans, $configDefinition);
    }

    /**
     * @return array
     */
    private static function getProperties()
    {
        $properties = [];
        $config     = new Config();
        $dir        = App::getAlias('@properties');

        if (is_readable($dir)) {
            $config->load($dir);
            $properties = $config->toArray();
        }

        return $properties;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private static function getCoreBean(string $type): array
    {
        $collector = BootBeanCollector::getCollector();
        if (!isset($collector[$type])) {
            return [];
        }

        $coreBeans = [];
        /** @var array $bootBeans */
        $bootBeans = $collector[$type];
        foreach ($bootBeans as $beanName) {
            /* @var \Swoft\Core\BootBeanInterface $bootBean */
            $bootBean  = App::getBean($beanName);
            $beans     = $bootBean->beans();
            $coreBeans = ArrayHelper::merge($coreBeans, $beans);
        }

        return $coreBeans;
    }

    /**
     * @return array
     */
    private static function getComponentDefinitions()
    {
        $definitions = [];
        $collector   = DefinitionCollector::getCollector();

        foreach ($collector as $className => $beanName) {
            /* @var \Swoft\Bean\DefinitionInterface $definition */
            $definition = App::getBean($beanName);

            $definitions = ArrayHelper::merge($definitions, $definition->getDefinitions());
        }

        return $definitions;
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        return self::$container;
    }
}
