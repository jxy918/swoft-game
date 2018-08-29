<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Devtool\Controller;

use Swoft\Aop\Aop;
use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Collector\PoolCollector;
use Swoft\Core\Config;
use Swoft\Devtool\Helper\DevToolHelper;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;
use Swoft\Http\Server\Payload;
use Swoft\Pool\PoolConfigInterface;

/**
 * Class AppController
 * @Controller(prefix="/__devtool/app")
 * @package Swoft\Devtool\Controller
 */
class AppController
{
    /**
     * get app info
     * @RequestMapping(route="env", method=RequestMethod::GET)
     * @return array
     */
    public function index(): array
    {
        return [
            'os' => \PHP_OS,
            'phpVersion' => \PHP_VERSION,
            'swooleVersion' => \SWOOLE_VERSION,
            'swoftVersion' => App::version(),
            'appName' => \APP_NAME,
            'basePath' => \BASE_PATH,
        ];
    }

    /**
     * get app config
     * @RequestMapping(route="config", method=RequestMethod::GET)
     * @param Request $request
     * @return array|mixed
     */
    public function config(Request $request)
    {
        if ($key = $request->query('key')) {
            /** @see Config::get() */
            return \bean('config')->get($key);
        }

        /** @see Config::toArray() */
        return \bean('config')->toArray();
    }

    /**
     * get app pools
     * @RequestMapping(route="pools", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    public function pools(Request $request): array
    {
        if ($name = $request->query('name')) {
            if (!App::hasPool($name)) {
                return [];
            }

            /** @var PoolConfigInterface $poolConfig */
            $poolConfig = App::getPool($name)->getPoolConfig();

            return $poolConfig->toArray();
        }

        return PoolCollector::getCollector();
    }

    /**
     * get app beans
     * @RequestMapping(route="beans", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function beans(Request $request): array
    {
        if ($name = $request->query('name')) {
            return [];
        }

        return BeanFactory::getContainer()->getBeanNames();
    }

    /**
     * get app beans config
     * @RequestMapping(route="beans-config", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function beansConfig(Request $request): array
    {
        if ($name = $request->query('name')) {
            return [];
        }

        return [];
    }

    /**
     * get app path aliases
     * @RequestMapping(route="aliases", method=RequestMethod::GET)
     * @return array
     */
    public function pathAliases(): array
    {
        return App::getAliases();
    }

    /**
     * get all registered application events list
     * @RequestMapping(route="events", method=RequestMethod::GET)
     * @param Request $request
     * @return Payload
     */
    public function events(Request $request): Payload
    {
        /** @var \Swoft\Event\EventManager $em */
        $em = \bean('eventManager');

        if ($event = \trim($request->query('name'))) {
            $queue = $em->getListenerQueue($event);

            if (!$queue) {
                return Payload::make(['msg' => 'event name is invalid: ' . $event],404);
            }

            $classes = [];

            foreach ($queue->getIterator() as $listener) {
                $classes[] = \get_parent_class($listener);
            }

            return Payload::make($classes);
        }

        return Payload::make($em->getListenedEvents());
    }

    /**
     * get all registered components
     * @RequestMapping(route="components", method=RequestMethod::GET)
     * @return array
     * @throws \InvalidArgumentException
     */
    public function components(): array
    {
        $lockFile = App::getAlias('@root/composer.lock');

        return DevToolHelper::parseComposerLockFile($lockFile);
    }

    /**
     * get all registered aop handlers
     * @RequestMapping(route="aop/handlers", method=RequestMethod::GET)
     * @return array
     */
    public function aopHandles(): array
    {
        /** @var Aop $aop */
        $aop = \bean(Aop::class);

        return $aop->getAspects();
    }

    /**
     * get all registered http middleware list
     * @RequestMapping(route="http/middles", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function httpMiddles(Request $request): array
    {
        /** @var \Swoft\Http\Server\ServerDispatcher $dispatcher */
        $dispatcher = \bean('serverDispatcher');

        $type = (int)$request->query('type');

        // 1 only return user's
        if ($type === 1) {
            return $dispatcher->getMiddlewares();
        }

        return $dispatcher->requestMiddleware();
    }

    /**
     * get all registered rpc middleware list
     * @RequestMapping(route="rpc/middles", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function rpcMiddles(Request $request): array
    {
        $bean = 'ServiceDispatcher';

        if (!App::hasBean($bean)) {
            return [];
        }

        /** @var \Swoft\Rpc\Server\ServiceDispatcher $dispatcher */
        $dispatcher = \bean($bean);

        $type = (int)$request->query('type');

        // 1 only return user's
        if ($type === 1) {
            return $dispatcher->getMiddlewares();
        }

        return $dispatcher->requestMiddleware();
    }
}
