<?php

namespace Swoft\Core;

use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\ExceptionHandlerCollector;
use Swoft\Helper\PhpHelper;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;

/**
 * the handler of error and exception
 *
 * @Bean()
 * @uses      ErrorHandler
 * @version   2018年01月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ErrorHandler
{

    /**
     * handle exception
     *
     * @param \Throwable $throwable
     *
     * @return \Swoft\Http\Message\Server\Response
     */
    public function handle(\Throwable $throwable)
    {
        try {
            $response = $this->doHandler($throwable);
        } catch (\Throwable $e) {
            $response = $this->handleThrowtable($e);
        }

        return $response;
    }

    /**
     * do handler
     *
     * @param \Throwable $throwable
     *
     * @return mixed|\Swoft\Http\Message\Server\Response
     * @throws \Exception
     */
    public function doHandler(\Throwable $throwable)
    {
        $exceptionClass = get_class($throwable);
        $collector      = ExceptionHandlerCollector::getCollector();
        $isNotExistHandler = !isset($collector[$exceptionClass]) && !isset($collector[\Exception::class]);
        if (empty($collector) || $isNotExistHandler) {
            return $this->handleThrowtable($throwable);
        }

        if(isset($collector[$exceptionClass])){
            list($classBeanName, $methodName) = $collector[$exceptionClass];
        }else{
            list($classBeanName, $methodName) = $collector[\Exception::class];
        }

        $handler    = App::getBean($classBeanName);
        $bindParams = $this->getBindParams($classBeanName, $methodName, $throwable);
        $response   = PhpHelper::call([$handler, $methodName], $bindParams);
        if ($response instanceof ResponseInterface) {
            return $response;
        }

        throw new \Exception("the handler of exception must be return the object of response!");
    }

    /**
     * handler throwable
     *
     * @param \Throwable $throwable
     *
     * @return \Swoft\Http\Message\Server\Response
     */
    private function handleThrowtable(\Throwable $throwable)
    {
        $message = sprintf("%s %s %d", $throwable->getFile(), $throwable->getMessage(), $throwable->getLine());

        /* @var \Swoft\Http\Message\Server\Response $response */
        $response = RequestContext::getResponse();
        $response = $response->json([$message]);

        return $response;
    }

    /**
     * get binded params
     *
     * @param string     $className
     * @param string     $methodName
     * @param \Throwable $throwable
     *
     * @return array
     */
    private function getBindParams(string $className, string $methodName, \Throwable $throwable)
    {
        $reflectClass  = new \ReflectionClass($className);
        $reflectMethod = $reflectClass->getMethod($methodName);
        $reflectParams = $reflectMethod->getParameters();
        $response      = RequestContext::getResponse();
        $request       = RequestContext::getRequest();

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
            if ($type === Request::class) {
                $bindParams[$key] = $request;
            } elseif ($type == Response::class) {
                $bindParams[$key] = $response;
            } elseif ($type == \Throwable::class) {
                $bindParams[$key] = $throwable;
            } else {
                $bindParams[$key] = null;
            }
        }

        return $bindParams;
    }
}