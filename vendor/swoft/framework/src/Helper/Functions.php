<?php

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Config\Config;
use Swoft\Context\Context;
use Swoft\Contract\ContextInterface;
use Swoft\Event\Manager\EventManager;
use Swoft\Exception\SwoftException;
use Swoft\Http\Server\HttpContext;
use Swoft\Http\Server\HttpServer;
use Swoft\Process\Context\ProcessContext;
use Swoft\Process\Context\UserProcessContext;
use Swoft\Rpc\Server\ServiceContext;
use Swoft\Server\Context\ShutdownContext;
use Swoft\Server\Context\StartContext;
use Swoft\Server\Context\WorkerStartContext;
use Swoft\Server\Context\WorkerStopContext;
use Swoft\Server\Server;
use Swoft\Task\FinishContext;
use Swoft\Task\TaskContext;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\Validator;
use Swoft\WebSocket\Server\WebSocketServer;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function env(string $key = null, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (defined($value)) {
            $value = constant($value);
        }

        return $value;
    }
}

if (!function_exists('alias')) {
    /**
     * @param string $key
     *
     * @return string
     */
    function alias(string $key): string
    {
        return Swoft::getAlias($key);
    }
}

if (!function_exists('event')) {
    /**
     * @return EventManager
     * @throws ReflectionException
     * @throws ContainerException
     */
    function event(): EventManager
    {
        return BeanFactory::getBean('eventManager');
    }
}

if (!function_exists('config')) {
    /**
     * Get value from config by key or default
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     */
    function config(string $key = null, $default = null)
    {
        if (!BeanFactory::hasBean('config')) {
            return sprintf('${.config.%s}', $key);
        }

        /* @var Config $config */
        $config = BeanFactory::getBean('config');

        return $config->get($key, $default);
    }
}

if (!function_exists('sgo')) {
    /**
     * Create coroutine like 'go()'
     * In the swoft, you must use `sgo()` instead of  swoole `go()` function
     *
     * @param callable $callable
     * @param bool     $wait
     *
     * @return int
     */
    function sgo(callable $callable, bool $wait = true): int
    {
        return \Swoft\Co::create($callable, $wait);
    }
}

if (!function_exists('srun')) {
    /**
     * @param callable $callable
     *
     * @return bool
     */
    function srun(callable $callable): bool
    {
        return \Swoft\Co::run($callable);
    }
}

if (!function_exists('context')) {
    /**
     * Get current context
     *
     * @return ContextInterface|HttpContext|ServiceContext|TaskContext|FinishContext|UserProcessContext|ProcessContext|StartContext|WorkerStartContext|WorkerStopContext|ShutdownContext
     * @throws SwoftException
     */
    function context(): ContextInterface
    {
        return Context::get(true);
    }
}

if (!function_exists('server')) {
    /**
     * Get server instance
     *
     * @return Server|HttpServer|WebSocketServer
     */
    function server(): Server
    {
        return Server::getServer();
    }
}

if (!function_exists('validate')) {
    /**
     * @param array  $data
     * @param string $validatorName
     * @param array  $fields
     * @param array  $userValidators
     *
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ValidatorException
     */
    function validate(array $data, string $validatorName, array $fields = [], array $userValidators = []): array
    {
        /* @var Validator $validator */
        $validator = BeanFactory::getBean('validator');
        return $validator->validate($data, $validatorName, $fields, $userValidators);
    }
}
