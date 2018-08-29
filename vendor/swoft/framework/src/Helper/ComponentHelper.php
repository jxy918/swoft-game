<?php

namespace Swoft\Helper;

/**
 * Component helper
 */
class ComponentHelper
{
    /**
     * @param string $component
     * @param string $path
     *
     * @return string
     */
    public static function getComponentNamespace(string $component, string $path):string
    {
        $composerFile = $path . DS . 'composer.json';
        $namespaceMapping = self::parseAutoloadFromComposerFile($composerFile);
        $ns = $namespaceMapping['src/'] ?? self::getDefaultNamespace($component);
        return $ns;
    }
    /**
     * Get the default namespace of component
     *
     * @param string $component
     * @return string
     */
    public static function getComponentNs(string $component): string
    {
        if ($component === 'framework') {
            return '';
        }

        $namespace = '';
        $nsAry = explode('-', $component);
        foreach ($nsAry as $ns) {
            $namespace .= "\\" . ucfirst($ns);
        }

        return $namespace;
    }

    /**
     * @param string $filename
     * @return array
     */
    private static function parseAutoloadFromComposerFile($filename): array
    {
        $json = file_get_contents($filename);
        $mapping = [];

        try {
            $content = JsonHelper::decode($json, true);
        } catch (\InvalidArgumentException $e) {
            $content = [];
        }

        // only compatible with psr-4 now
        //TODO compatible with the another autoload standard
        if (isset($content['autoload']['psr-4'])) {
            $mapping = $content['autoload']['psr-4'];
            $mapping = array_flip($mapping);
            foreach ($mapping as $key => $value) {
                $valueLength = \strlen($value);
                $mapping[$key] = $value[$valueLength - 1] === '\\' ? substr($value, 0, $valueLength - 1) : $value;
            }
        }

        return \is_array($mapping) ? $mapping : [];
    }

    /**
     * @param $component
     * @return string
     */
    private static function getDefaultNamespace($component): string
    {
        $componentNs = ComponentHelper::getComponentNs($component);
        $componentNs = self::handlerFrameworkNamespace($componentNs);
        $namespace = "Swoft{$componentNs}";

        return $namespace;
    }

    /**
     * @param string $componentNs
     *
     * @return string
     */
    private static function handlerFrameworkNamespace(string  $componentNs):string
    {
        if($componentNs === '\Swoft\Framework'){
            return '';
        }

        return $componentNs;
    }
}
