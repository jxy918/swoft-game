<?php declare(strict_types=1);

namespace Swoft\Aop;

use Swoft\Aop\Ast\Visitor\ProxyVisitor;
use Swoft\Proxy\Exception\ProxyException;
use Swoft\Proxy\Proxy as BaseProxy;

/**
 * Class Proxy
 *
 * @since 2.0
 */
class Proxy
{
    /**
     * New class name by proxy
     *
     * @param string $className
     *
     * @return string
     * @throws ProxyException
     */
    public static function newClassName(string $className): string
    {
        // Only proxy AOP class
        if (!Aop::matchClass($className)) {
            return $className;
        }

        // Ignore aop proxy
        if (strpos($className, '_IGNORE_') !== false) {
            return $className;
        }

        $visitor = new ProxyVisitor();
        return BaseProxy::newClassName($className, $visitor);
    }

    /**
     * Get real class name
     *
     * @param string $proxyClassName
     *
     * @return string
     */
    public static function getClassName(string $proxyClassName): string
    {
        list($className) = explode('_', $proxyClassName);
        return $className;
    }

    /**
     * Get original class name
     *
     * @param string $proxyClassName
     *
     * @return string
     */
    public static function getOriginalClassName(string $proxyClassName): string
    {
        $proxys = explode(ProxyVisitor::PROXY, $proxyClassName);

        return $proxys[0];
    }
}
