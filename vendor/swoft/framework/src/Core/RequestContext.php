<?php

namespace Swoft\Core;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swoft\App;
use Swoft\Helper\ArrayHelper;

/**
 * Class RequestContext
 *
 * @package Swoft\Core
 */
class RequestContext
{
    /**
     * Key of request context share data
     */
    const DATA_KEY = 'data';

    /**
     * Key of current Request
     */
    const REQUEST_KEY = 'request';

    /**
     * Key of current Response
     */
    const RESPONSE_KEY = 'response';

    /**
     * @var array Coroutine context
     */
    private static $context;

    /**
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public static function getRequest()
    {
        return self::getCoroutineContext(self::REQUEST_KEY);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public static function getResponse()
    {
        return self::getCoroutineContext(self::RESPONSE_KEY);
    }

    /**
     * @return array|null
     */
    public static function getContextData()
    {
        return self::getCoroutineContext(self::DATA_KEY);
    }

    /**
     * Set the object of request
     *
     * @param RequestInterface $request
     */
    public static function setRequest(RequestInterface $request)
    {
        $coroutineId = self::getCoroutineId();
        self::$context[$coroutineId][self::REQUEST_KEY] = $request;
    }

    /**
     * Set the object of response
     *
     * @param ResponseInterface $response
     */
    public static function setResponse(ResponseInterface $response)
    {
        $coroutineId = self::getCoroutineId();
        self::$context[$coroutineId][self::RESPONSE_KEY] = $response;
    }

    /**
     * Set the context data
     *
     * @param array $contextData
     */
    public static function setContextData(array $contextData = [])
    {
        $existContext = [];
        $coroutineId = self::getCoroutineId();
        if (isset(self::$context[$coroutineId][self::DATA_KEY])) {
            $existContext = self::$context[$coroutineId][self::DATA_KEY];
        }
        self::$context[$coroutineId][self::DATA_KEY] = ArrayHelper::merge($contextData, $existContext);
    }

    /**
     * Update context data by key
     *
     * @param string $key
     * @param mixed $val
     */
    public static function setContextDataByKey(string $key, $val)
    {
        $coroutineId = self::getCoroutineId();
        self::$context[$coroutineId][self::DATA_KEY][$key] = $val;
    }

    /**
     * Get context data by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getContextDataByKey(string $key, $default = null)
    {
        $coroutineId = self::getCoroutineId();
        if (isset(self::$context[$coroutineId][self::DATA_KEY][$key])) {
            return self::$context[$coroutineId][self::DATA_KEY][$key];
        }

        return $default;
    }

    /**
     * Update context data by child key
     *
     * @param string $key
     * @param string $child
     * @param mixed $val
     */
    public static function setContextDataByChildKey(string $key, string $child, $val)
    {
        $coroutineId = self::getCoroutineId();
        self::$context[$coroutineId][self::DATA_KEY][$key][$child] = $val;
    }

    /**
     * Get context data by child key
     *
     * @param string $key
     * @param string $child
     * @param mixed $default
     * @return mixed
     */
    public static function getContextDataByChildKey(string $key, string $child, $default = null)
    {
        $coroutineId = self::getCoroutineId();
        if (isset(self::$context[$coroutineId][self::DATA_KEY][$key][$child])) {
            return self::$context[$coroutineId][self::DATA_KEY][$key][$child];
        }

        return $default;
    }

    /**
     * Get context data by child key
     *
     * @param string $key
     * @param string $child
     */
    public static function removeContextDataByChildKey(string $key, string $child)
    {
        $coroutineId = self::getCoroutineId();
        unset(self::$context[$coroutineId][self::DATA_KEY][$key][$child]);
    }

    /**
     * Get Current Request Log ID
     *
     * @return string
     */
    public static function getLogid(): string
    {
        $contextData = self::getCoroutineContext(self::DATA_KEY);
        $logid = $contextData['logid'] ?? '';
        return $logid;
    }

    /**
     * Get Current Request Span ID
     *
     * @return int
     */
    public static function getSpanid(): int
    {
        $contextData = self::getCoroutineContext(self::DATA_KEY);

        return (int)($contextData['spanid'] ?? 0);
    }

    /**
     * Destroy all current coroutine context data
     */
    public static function destroy()
    {
        $coroutineId = self::getCoroutineId();
        if (isset(self::$context[$coroutineId])) {
            unset(self::$context[$coroutineId]);
        }
    }

    /**
     * Get data from coroutine context by key
     *
     * @param string $key key of context
     * @return mixed|null
     */
    private static function getCoroutineContext(string $key)
    {
        $coroutineId = self::getCoroutineId();
        if (!isset(self::$context[$coroutineId])) {
            return null;
        }

        $coroutineContext = self::$context[$coroutineId];
        if (isset($coroutineContext[$key])) {
            return $coroutineContext[$key];
        }
        return null;
    }

    /**
     * Get current coroutine ID
     *
     * @return int|null Return null when in non-coroutine context
     */
    private static function getCoroutineId()
    {
        return Coroutine::tid();
    }
}
