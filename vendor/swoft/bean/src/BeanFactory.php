<?php declare(strict_types=1);

namespace Swoft\Bean;

use ReflectionException;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Bean\Contract\HandlerInterface;
use Swoft\Stdlib\Reflections;

/**
 * Class BeanFactory
 *
 * @since 2.0
 */
class BeanFactory
{
    /**
     * Init
     *
     * @return void
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public static function init(): void
    {
        Container::getInstance()->init();
    }

    /**
     * @return array
     */
    public static function getStats(): array
    {
        return Container::getInstance()->getStats();
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        return Container::getInstance();
    }

    /*****************************************************************************
     * Bean manage
     ****************************************************************************/

    /**
     * Get object by name
     *
     * @param string $name Bean name Or alias Or class name
     *
     * @return object|mixed
     */
    public static function getBean(string $name)
    {
        return Container::getInstance()->get($name);
    }

    /**
     * Many instance of one class
     *
     * @param string $className
     *
     * @return array
     */
    public static function getBeans(string $className): array
    {
        return Container::getInstance()->gets($className);
    }

    /**
     * Get an singleton bean instance
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getSingleton(string $name)
    {
        return Container::getInstance()->getSingleton($name);
    }

    /**
     * Whether has bean
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasBean(string $name): bool
    {
        return Container::getInstance()->has($name);
    }

    /**
     * Whether is singleton
     *
     * @param string $name
     *
     * @return bool
     */
    public static function isSingleton(string $name): bool
    {
        return Container::getInstance()->isSingleton($name);
    }

    /**
     * Create bean by definition
     *
     * @param string $name
     * @param array  $definition
     *
     * @return object
     * @example
     *
     * $bean = BeanFactory::createBean('className');
     *
     * $bean = BeanFactory::createBean('name', [
     *     'class' => 'className',
     *     ......
     * ]);
     *
     * @return object
     */
    public static function createBean(string $name, array $definition = [])
    {
        return Container::getInstance()->create($name, $definition);
    }

    /*****************************************************************************
     * Session bean manage
     ****************************************************************************/

    /**
     * Get request bean
     *
     * @param string $name
     * @param string $id
     *
     * @return object|mixed
     */
    public static function getRequestBean(string $name, string $id)
    {
        return Container::getInstance()->getRequest($name, $id);
    }

    /**
     * Get session bean
     *
     * @param string $name
     * @param string $sid
     *
     * @return object|mixed
     */
    public static function getSessionBean(string $name, string $sid)
    {
        return Container::getInstance()->getSession($name, $sid);
    }

    /**
     * Destroy request bean
     *
     * @param string $id
     */
    public static function destroyRequest(string $id): void
    {
        Container::getInstance()->destroyRequest($id);
    }

    /**
     * Destroy session bean
     *
     * @param string $sid
     */
    public static function destroySession(string $sid): void
    {
        Container::getInstance()->destroySession($sid);
    }

    /*****************************************************************************
     * Other methods
     ****************************************************************************/

    /**
     * Add definitions
     *
     * @param array $definitions
     *
     * @return void
     */
    public static function addDefinitions(array $definitions): void
    {
        Container::getInstance()->addDefinitions($definitions);
    }

    /**
     * Add annotations
     *
     * @param array $annotations
     *
     * @return void
     */
    public static function addAnnotations(array $annotations): void
    {
        Container::getInstance()->addAnnotations($annotations);
    }

    /**
     * Add annotation parsers
     *
     * @param array $annotationParsers
     *
     * @return void
     */
    public static function addParsers(array $annotationParsers): void
    {
        Container::getInstance()->addParsers($annotationParsers);
    }

    /**
     * Set bean handler
     *
     * @param HandlerInterface $handler
     */
    public static function setHandler(HandlerInterface $handler): void
    {
        Container::getInstance()->setHandler($handler);
    }

    /**
     * @param string $className
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getReflection(string $className): array
    {
        return Reflections::get($className);
    }
}
