<?php declare(strict_types=1);

namespace Swoft\Devtool\Http\Controller;

use Swoft\Aop\Aop;
use Swoft\Bean\BeanFactory;
use Swoft\Config\Config;
use Swoft\Devtool\Helper\DevToolHelper;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class AppController
 *
 * @Controller(prefix="/__devtool/app")
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
            'os'            => \PHP_OS,
            'phpVersion'    => \PHP_VERSION,
            'swooleVersion' => \SWOOLE_VERSION,
            'swoftVersion'  => \Swoft::VERSION,
            'appName'       => \APP_NAME,
            'basePath'      => \BASE_PATH,
        ];
    }

    /**
     * Get app config
     * @RequestMapping(route="config", method=RequestMethod::GET)
     * @param Request $request
     * @return array|mixed
     * @throws \Throwable
     */
    public function config(Request $request)
    {
        if ($key = $request->query('key')) {
            /** @see Config::get() */
            return \config($key);
        }

        /** @see Config::toArray() */
        return \bean('config')->toArray();
    }

    /**
     * get app pools
     * @RequestMapping(route="pools", method=RequestMethod::GET)
     * @param Request $request
     * @return array
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

        return BeanFactory::getContainer()->getNames();
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
        return \Swoft::getAliases();
    }

    /**
     * Get all registered application events list
     * @RequestMapping(route="events", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function events(Request $request): array
    {
        /** @var \Swoft\Event\Manager\EventManager $em */
        $em = \bean('eventManager');

        if ($event = \trim($request->query('name'))) {
            if (!$queue = $em->getListenerQueue($event)) {
                return ['msg' => 'event name is invalid: ' . $event];
            }

            $classes = [];
            foreach ($queue->getIterator() as $listener) {
                $classes[] = \get_class($listener);
            }

            return $classes;
        }

        return $em->getListenedEvents();
    }

    /**
     * Get all registered components
     *
     * @RequestMapping(route="components", method=RequestMethod::GET)
     * @return array
     * @throws \InvalidArgumentException
     */
    public function components(): array
    {
        $lockFile = \Swoft::getAlias('@base/composer.lock');

        return DevToolHelper::parseComposerLockFile($lockFile);
    }

    /**
     * Get all registered aop handlers
     *
     * @RequestMapping(route="aop/handlers", method=RequestMethod::GET)
     * @return array
     * @throws \Throwable
     */
    public function aopHandles(): array
    {
        /** @var Aop $aop */
        $aop = \bean(Aop::class);

        return $aop->getAspects();
    }

    /**
     * Get all registered http middleware list
     *
     * @RequestMapping(route="http/middles", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function httpMiddles(Request $request): array
    {
        /** @var \Swoft\Http\Server\HttpDispatcher $dispatcher */
        $dispatcher = \bean('serverDispatcher');
        $middleType = (int)$request->query('type');

        // 1: only return user's
        if ($middleType === 1) {
            return $dispatcher->getMiddlewares();
        }

        return $dispatcher->requestMiddleware();
    }

    /**
     * get all registered rpc middleware list
     * @RequestMapping(route="rpc/middles", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function rpcMiddles(Request $request): array
    {
        $beanName = 'serviceDispatcher';
        if (!\Swoft::hasBean($beanName)) {
            return [];
        }

        /** @var \Swoft\Rpc\Server\ServiceDispatcher $dispatcher */
        $dispatcher = \bean($beanName);
        $middleType = (int)$request->query('type');

        // 1 only return user's
        if ($middleType === 1) {
            return $dispatcher->getMiddlewares();
        }

        return $dispatcher->requestMiddleware();
    }
}
