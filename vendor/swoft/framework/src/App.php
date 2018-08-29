<?php

namespace Swoft;

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Collector\PoolCollector;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Core\Application;
use Swoft\Core\ApplicationContext;
use Swoft\Core\Config;
use Swoft\Core\Timer;
use Swoft\Exception\InvalidArgumentException;
use Swoft\Log\Logger;
use Swoft\Pool\PoolInterface;
use Swoft\Redis\Pool\RedisPool;
use Swoole\Coroutine as SwCoroutine;

/**
 * 应用简写类
 *
 * @author    stelin <phpcrazy@126.com>
 */
class App
{
    /**
     * 应用对象
     *
     * @var Application
     */
    public static $app;

    /**
     * 服务器对象
     *
     * @var AbstractServer|\Swoft\Http\Server\Http\HttpServer|\Swoft\WebSocket\Server\WebSocketServer
     */
    public static $server;

    /**
     * config bean配置对象
     *
     * @var Config
     */
    public static $properties;

    /**
     * Swoft系统配置对象
     *
     * @var Config
     */
    public static $appProperties;

    /**
     * 是否初始化了crontab
     *
     * @var bool
     */
    public static $isInitCron = false;

    /**
     * 是否处于自动化测试流程中
     *
     * @var bool
     */
    public static $isInTest = false;

    /**
     * 别名库
     *
     * @var array
     */
    private static $aliases = [
        '@swoft' => __DIR__,
    ];

    /**
     * 获取mysqlBean对象
     */
    public static function getMysqlPool()
    {
        return self::getBean('mysql');
    }

    /**
     * swoft版本
     *
     * @return string
     */
    public static function version(): string
    {
        return '1.0.0';
    }

    /**
     * redis连接池
     *
     * @return RedisPool
     */
    public static function getRedisPool(): RedisPool
    {
        return self::getBean('redisPool');
    }

    /**
     * has bean
     *
     * @param string $name 名称
     * @return bool
     */
    public static function hasBean(string $name): bool
    {
        return BeanFactory::hasBean($name);
    }

    /**
     * get bean
     *
     * @param string $name 名称
     *
     * @return mixed
     */
    public static function getBean(string $name)
    {
        return ApplicationContext::getBean($name);
    }

    /**
     * @return Application
     */
    public static function getApplication(): Application
    {
        return ApplicationContext::getBean('application');
    }

    /**
     * 获取config bean
     *
     * @return Config
     */
    public static function getProperties(): Config
    {
        return ApplicationContext::getBean('config');
    }

    /**
     * 初始化配置对象
     *
     * @param Config $properties 容器中config对象
     */
    public static function setProperties($properties = null)
    {
        if ($properties === null) {
            $properties = self::getProperties();
        }

        self::$properties = $properties;
    }

    /**
     * @return Config
     */
    public static function getAppProperties(): Config
    {
        return self::$appProperties;
    }

    /**
     * @param Config $appProperties
     */
    public static function setAppProperties(Config $appProperties)
    {
        self::$appProperties = $appProperties;
    }

    /**
     * 日志对象
     *
     * @return Logger
     */
    public static function getLogger(): Logger
    {
        return ApplicationContext::getBean('logger');
    }

    /**
     * get pool by name
     *
     * @param string $name
     *
     * @return PoolInterface
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    public static function getPool(string $name): PoolInterface
    {
        $collector = PoolCollector::getCollector();
        if (!isset($collector[$name])) {
            throw new InvalidArgumentException("the pool of $name is not exist!");
        }

        $poolBeanName = $collector[$name];

        return self::getBean($poolBeanName);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function hasPool(string $name): bool
    {
        $collector = PoolCollector::getCollector();

        return isset($collector[$name]);
    }

    /**
     * 获取定时器bean
     *
     * @return Timer
     */
    public static function getTimer(): Timer
    {
        return ApplicationContext::getBean('timer');
    }

    /**
     * 触发事件
     *
     * @param string|\Swoft\Event\EventInterface $event  发布的事件名称|对象
     * @param mixed                              $target
     * @param array                              $params 附加数据信息
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function trigger($event, $target = null, ...$params)
    {
        /** @var \Swoft\Event\EventManager $em */
        $em = ApplicationContext::getBean('eventManager');

        return $em->trigger($event, $target, $params);
    }

    /**
     * 注册多个别名
     *
     * @param array $aliases 别名数组
     *                       <pre>
     *                       [
     *                       '@root' => BASE_PATH
     *                       ......
     *                       ]
     *                       </pre>
     *
     * @throws \InvalidArgumentException
     */
    public static function setAliases(array $aliases)
    {
        foreach ($aliases as $name => $path) {
            self::setAlias($name, $path);
        }
    }

    /**
     * Set alias
     *
     * @param string $alias alias
     * @param string $path  path
     *
     * @throws \InvalidArgumentException
     */
    public static function setAlias(string $alias, string $path = null)
    {
        if ($alias[0] !== '@') {
            $alias = '@' . $alias;
        }

        // Delete alias
        if (!$path) {
            unset(self::$aliases[$alias]);

            return;
        }

        // $path 不是别名，直接设置
        if ($path[0] !== '@') {
            self::$aliases[$alias] = $path;

            return;
        }

        // $path是一个别名
        if (isset(self::$aliases[$path])) {
            self::$aliases[$alias] = self::$aliases[$path];

            return;
        }

        list($root) = explode('/', $path);
        if (!isset(self::$aliases[$root])) {
            throw new \InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = str_replace($root, '', $path);

        self::$aliases[$alias] = $rootPath . $aliasPath;
    }

    /**
     * Get alias
     *
     * @param string $alias
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getAlias(string $alias): string
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return $alias;
        }

        if (isset(self::$aliases[$alias])) {
            return self::$aliases[$alias];
        }

        list($root) = \explode('/', $alias, 2);
        if (!isset(self::$aliases[$root])) {
            throw new \InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = \str_replace($root, '', $alias);

        return $rootPath . $aliasPath;
    }

    /**
     * Is alias exist ?
     *
     * @param string $alias
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public static function hasAlias(string $alias): bool
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return false;
        }

        return isset(self::$aliases[$alias]);
    }

    /**
     * trace级别日志
     *
     * @param mixed $message 日志信息
     * @param array $context 附加信息
     */
    public static function trace($message, array $context = array())
    {
        self::getLogger()->addTrace($message, $context);
    }

    /**
     * error级别日志
     *
     * @param mixed $message 日志信息
     * @param array $context 附加信息
     */
    public static function error($message, array $context = array())
    {
        self::getLogger()->error($message, $context);
    }

    /**
     * info级别日志
     *
     * @param mixed $message 日志信息
     * @param array $context 附加信息
     */
    public static function info($message, array $context = array())
    {
        self::getLogger()->info($message, $context);
    }

    /**
     * warning级别日志
     *
     * @param mixed $message 日志信息
     * @param array $context 附加信息
     */
    public static function warning($message, array $context = array())
    {
        self::getLogger()->warning($message, $context);
    }

    /**
     * debug级别日志
     *
     * @param mixed $message 日志信息
     * @param array $context 附加信息
     */
    public static function debug($message, array $context = array())
    {
        self::getLogger()->debug($message, $context);
    }

    /**
     * 标记日志
     *
     * @param string $key 统计key
     * @param mixed  $val 统计值
     */
    public static function pushlog($key, $val)
    {
        self::getLogger()->pushLog($key, $val);
    }

    /**
     * 统计标记开始
     *
     * @param string $name 标记名
     */
    public static function profileStart(string $name)
    {
        self::getLogger()->profileStart($name);
    }

    /**
     * 统计标记结束
     *
     * @param string $name 标记名，必须和开始标记名称一致
     */
    public static function profileEnd($name)
    {
        self::getLogger()->profileEnd($name);
    }

    /**
     * @return bool 当前是否是worker状态
     */
    public static function isWorkerStatus(): bool
    {
        if (self::$server === null) {
            return false;
        }

        $server = self::$server->getServer();

        if ($server && \property_exists($server, 'taskworker') && ($server->taskworker === false)) {
            return true;
        }

        return false;
    }

    /**
     * Get workerId
     */
    public static function getWorkerId(): int
    {
        if (self::$server === null) {
            return 0;
        }

        $server = self::$server->getServer();

        if ($server && \property_exists($server, 'worker_id') && $server->worker_id > 0) {
            return $server->worker_id;
        }

        return 0;
    }

    /**
     * Whether it is coroutine context
     *
     * @return bool
     */
    public static function isCoContext(): bool
    {
        return SwCoroutine::getuid() > 0;
    }

    /**
     * 命中率计算
     *
     * @param string $name  名称
     * @param int    $hit   命中
     * @param int    $total 总共
     */
    public static function counting(string $name, int $hit, $total = null)
    {
        self::getLogger()->counting($name, $hit, $total);
    }

    /**
     * @return array
     */
    public static function getAliases(): array
    {
        return self::$aliases;
    }
}
