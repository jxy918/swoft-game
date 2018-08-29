<?php

namespace Swoft\Console\Router;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bootstrap\Boots\Bootable;
use Swoft\Bootstrap\Boots\InitMbFunsEncoding;
use Swoft\Bootstrap\Boots\LoadEnv;
use Swoft\Bootstrap\Boots\LoadInitConfiguration;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Core\Coroutine;
use Swoft\Core\RequestContext;
use Swoft\Helper\PhpHelper;

/**
 * The adapter of command
 * @Bean()
 */
class HandlerAdapter
{
    /**
     * @param array $handler
     * @return void
     * @throws \ReflectionException
     */
    public function doHandler(array $handler)
    {
        list($className, $method, $coroutine, $server) = $handler;

        $bindParams = $this->getBindParams($className, $method);
        $class = App::getBean($className);
        if ($coroutine) {
            $this->executeCommandByCoroutine($class, $method, $server, $bindParams);
        } else {
            $this->executeCommand($class, $method, $server, $bindParams);
        }
    }

    /**
     * get binded params
     *
     * @param string $className
     * @param string $methodName
     * @return array
     * @throws \ReflectionException
     */
    private function getBindParams(string $className, string $methodName): array
    {
        $reflectClass = new \ReflectionClass($className);
        $reflectMethod = $reflectClass->getMethod($methodName);
        $reflectParams = $reflectMethod->getParameters();

        // binding params
        $bindParams = [];
        foreach ($reflectParams as $key => $reflectParam) {
            $reflectType = $reflectParam->getType();

            // undefined type of the param
            if ($reflectType === null) {
                $bindParams[$key] = null;
                continue;
            }

            /**
             * defined type of the param
             * @notice \ReflectType::getName() is not supported in PHP 7.0, that is why use __toString()
             */
            $type = $reflectType->__toString();
            if ($type === Output::class) {
                $bindParams[$key] = \output();
            } elseif ($type === Input::class) {
                $bindParams[$key] = \input();
            } else {
                $bindParams[$key] = null;
            }
        }

        return $bindParams;
    }

    /**
     * execute command by coroutine
     *
     * @param mixed $class
     * @param string $method
     * @param bool   $server
     * @param array  $bindParams
     */
    private function executeCommandByCoroutine($class, string $method, bool $server, $bindParams)
    {
        Coroutine::create(function () use ($class, $method, $server, $bindParams) {
            $this->beforeCommand(\get_parent_class($class), $method, $server);
            PhpHelper::call([$class, $method], $bindParams);
            $this->afterCommand($method, $server);
        });
    }

    /**
     * execute command
     *
     * @param mixed $class
     * @param string $method
     * @param bool   $server
     * @param array  $bindParams
     */
    private function executeCommand($class, string $method, bool $server, $bindParams)
    {
        $this->beforeCommand(\get_parent_class($class), $method, $server);
        PhpHelper::call([$class, $method], $bindParams);
        $this->afterCommand($method, $server);
    }

    /**
     * before command
     *
     * @param string $class
     * @param string $command
     * @param bool   $server
     */
    private function beforeCommand(string $class, string $command, bool $server)
    {
        if ($server) {
            return;
        }
        $this->bootstrap();
        BeanFactory::reload();

        // 初始化
        $spanId = 0;
        $logId = uniqid();

        $uri = $class . '->' . $command;
        $contextData = [
            'logid'       => $logId,
            'spanid'      => $spanId,
            'uri'         => $uri,
            'requestTime' => microtime(true),
        ];

        RequestContext::setContextData($contextData);
    }

    /**
     * after command
     *
     * @param string $command
     * @param bool   $server
     */
    private function afterCommand(string $command, bool $server)
    {
        if ($server) {
            return;
        }

        App::getLogger()->appendNoticeLog(true);
    }

    /**
     * bootstrap
     */
    private function bootstrap()
    {
        $defaultItems = [
            InitMbFunsEncoding::class,
            LoadEnv::class,
            LoadInitConfiguration::class,
        ];
        foreach ($defaultItems as $bootstrapItem) {
            if (class_exists($bootstrapItem)) {
                $itemInstance = new $bootstrapItem();
                if ($itemInstance instanceof Bootable) {
                    $itemInstance->bootstrap();
                }
            }
        }
    }
}
