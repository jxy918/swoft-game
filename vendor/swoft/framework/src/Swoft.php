<?php declare(strict_types=1);

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Container;
use Swoft\Concern\PathAliasTrait;
use Swoft\Event\EventInterface;
use Swoft\Event\Manager\EventManager;
use Swoft\Http\Server\HttpServer;
use Swoft\I18n\I18n;
use Swoft\Server\Server as SwoftServer;
use Swoft\Stdlib\Reflections;
use Swoft\SwoftApplication;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoole\Server;

/**
 * Swoft is a helper class serving common framework functions.
 *
 * @since 2.0
 */
class Swoft
{
    use PathAliasTrait;

    /**
     * Swoft version
     */
    public const VERSION = '2.0.5-beta';

    /**
     * Swoft log
     */
    public const FONT_LOGO = "
  ____                __ _     _____                                            _      ____         
 / ___|_      _____  / _| |_  |  ___| __ __ _ _ __ ___   _____      _____  _ __| | __ |___ \  __  __
 \___ \ \ /\ / / _ \| |_| __| | |_ | '__/ _` | '_ ` _ \ / _ \ \ /\ / / _ \| '__| |/ /   __) | \ \/ /
  ___) \ V  V / (_) |  _| |_  |  _|| | | (_| | | | | | |  __/\ V  V / (_) | |  |   <   / __/ _ >  < 
 |____/ \_/\_/ \___/|_|  \__| |_|  |_|  \__,_|_| |_| |_|\___| \_/\_/ \___/|_|  |_|\_\ |_____(_)_/\_\                                                                                                    
";

    /**
     * @var SwoftApplication
     */
    public static $app;

    /**
     * @return SwoftApplication
     */
    public static function app(): SwoftApplication
    {
        return self::$app;
    }

    /**
     * Get main server instance
     *
     * @return SwoftServer|HttpServer|WebSocketServer
     */
    public static function server(): SwoftServer
    {
        return SwoftServer::getServer();
    }

    /**
     * Get swoole server
     *
     * @return Server
     */
    public static function swooleServer(): Server
    {
        return self::server()->getSwooleServer();
    }

    /*******************************************************************************
     * bean short methods
     ******************************************************************************/

    /**
     * Whether has bean
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasBean(string $name): bool
    {
        return Container::$instance->has($name);
    }

    /**
     * Get bean object by name
     *
     * @param string $name Bean name Or alias Or class name
     *
     * @return object|mixed
     */
    public static function getBean(string $name)
    {
        return Container::$instance->get($name);
    }

    /**
     * @see Container::getSingleton()
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getSingleton(string $name)
    {
        return Container::$instance->getSingleton($name);
    }

    /*******************************************************************************
     * Some short methods
     ******************************************************************************/

    /**
     * Get an ReflectionClass object by input class.
     *
     * @param string $class
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getReflection(string $class): array
    {
        return Reflections::get($class);
    }

    /**
     * Trigger an swoft application event
     *
     * @param string|EventInterface $event eg: 'app.start' 'app.stop'
     * @param null|mixed            $target
     * @param array                 $params
     *
     * @return EventInterface
     */
    public static function trigger($event, $target = null, ...$params): EventInterface
    {
        /** @see EventManager::trigger() */
        return BeanFactory::getSingleton('eventManager')->trigger($event, $target, $params);
    }

    /**
     * @param string $key
     * @param array  $params
     * @param string $locale
     *
     * @return string
     */
    public static function t(string $key, array $params, string $locale = ''): string
    {
        /* @var I18n $i18n */
        $i18n = BeanFactory::getBean('i18n');

        return $i18n->translate($key, $params, $locale);
    }

    /**
     * Trigger an swoft application event. like self::trigger(), but params is array
     *
     * @param string|EventInterface $event
     * @param null|mixed            $target
     * @param array                 $params
     *
     * @return EventInterface
     */
    public static function triggerByArray($event, $target = null, array $params = []): EventInterface
    {
        /** @see EventManager::trigger() */
        return BeanFactory::getSingleton('eventManager')->trigger($event, $target, $params);
    }
}
