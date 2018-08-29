<?php

namespace Swoft\Rpc\Client\Bootstrap;

use Swoft\Bean\Annotation\Definition;
use Swoft\Bean\DefinitionInterface;
use Swoft\Rpc\Client\Bean\Collector\ReferenceCollector;
use Swoft\Rpc\Client\Service\ServiceProxy;

/**
 * The definition of rpc client
 * @Definition()
 */
class RpcClientDefinition implements DefinitionInterface
{
    /**
     * array
     */
    public function getDefinitions()
    {
        $definitions = [];
        $collector   = ReferenceCollector::getCollector();
        foreach ($collector as $className => $interfaceAry) {
            list($name, $interfaceClass, $version, $pool, $breaker, $packer, $fallback) = $interfaceAry;
            ServiceProxy::loadProxyClass($className, $interfaceClass);

            $definitions[$className] = [
                'class'       => $className,
                'name'        => $name,
                'version'     => $version,
                'poolName'    => $pool,
                'breakerName' => $breaker,
                'packerName'  => $packer,
                'interface'   => $interfaceClass,
                'fallback'    => $fallback
            ];
        }
        return $definitions;
    }
}