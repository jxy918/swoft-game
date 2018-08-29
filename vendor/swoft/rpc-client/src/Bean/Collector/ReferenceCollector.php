<?php

namespace Swoft\Rpc\Client\Bean\Collector;

use PhpDocReader\PhpDocReader;
use Swoft\Bean\CollectorInterface;
use Swoft\Rpc\Client\Bean\Annotation\Reference;
use Swoft\Rpc\Client\Exception\RpcClientException;

/**
 * The collector referece
 */
class ReferenceCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $references = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     *
     * @return mixed
     * @throws RpcClientException
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof Reference) {
            // phpdoc解析
            $phpReader     = new PhpDocReader();
            $property      = new \ReflectionProperty($className, $propertyName);
            $propertyClass = $phpReader->getPropertyClass($property);
            $name          = $objectAnnotation->getName();
            $pool          = $objectAnnotation->getPool();
            $version       = $objectAnnotation->getVersion();
            $breaker       = $objectAnnotation->getBreaker();
            $packer        = $objectAnnotation->getBreaker();
            $fallback      = $objectAnnotation->getFallback();

            $className      = sprintf("%s.%s.%s.%s.%s.%s", $name, $propertyClass, $version, $pool, $breaker, $packer);
            $className      = md5($className);
            $proxyClassName = str_replace("\\", '_', $propertyClass);
            $className      = $proxyClassName . '_' . $className;

            if (empty($name)) {
                throw new RpcClientException('@Reference must be define name! ');
            }
            if (isset(self::$references[$className])) {
                return $className;
            }

            self::$references[$className] = [
                $name,
                $propertyClass,
                $version,
                $pool,
                $breaker,
                $packer,
                $fallback
            ];

            return $className;
        }

        return null;
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$references;
    }
}