<?php

namespace Swoft\Helper;


use Composer\Autoload\ClassLoader;

class ComposerHelper
{

    /**
     * @var ClassLoader|mixed
     */
    static $loader;

    /**
     * @return ClassLoader
     */
    public static function getLoader(): ClassLoader
    {
        if (! self::$loader) {
            $loader = self::findLoader();
            $loader instanceof ClassLoader && self::$loader = $loader;
        }
        return self::$loader;
    }

    /**
     * @return ClassLoader
     * @throws \RuntimeException When Composer loader not found
     */
    public static function findLoader(): ClassLoader
    {
        $composerClass = '';
        foreach (get_declared_classes() as $declaredClass) {
            if (StringHelper::startsWith($declaredClass, 'ComposerAutoloaderInit') && method_exists($declaredClass, 'getLoader')) {
                $composerClass = $declaredClass;
                break;
            }
        }
        if (! $composerClass) {
            throw new \RuntimeException('Composer loader not found.');
        }
        return $composerClass::getLoader();
    }

    /**
     * @param string $namespace
     * @return string
     */
    public static function getDirByNamespace(string $namespace): string
    {
        $dir = '';
        $loader = self::findLoader();
        $prefixesPsr4 = $loader->getPrefixesPsr4();
        $maxLength = 0;
        foreach ($prefixesPsr4 as $prefix => $path) {
            if (StringHelper::startsWith($namespace, $prefix)) {
                $strLen = strlen($prefix);
                if ($strLen > $maxLength) {
                    $dir = current($path) . DIRECTORY_SEPARATOR . substr($namespace, $strLen);
                }
            }
        }
        return $dir;
    }

}
