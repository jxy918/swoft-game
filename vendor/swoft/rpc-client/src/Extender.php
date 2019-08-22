<?php declare(strict_types=1);


namespace Swoft\Rpc\Client;


use function context;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Client\Contract\ExtenderInterface;

/**
 * Class Extender
 *
 * @since 2.0
 *
 * @Bean(name="rpcClientExtender")
 */
class Extender implements ExtenderInterface
{
    /**
     * @return array
     */
    public function getExt(): array
    {
        return [
            context()->get('traceid', ''),
            context()->get('spanid', ''),
            context()->get('parentid', ''),
        ];
    }
}