<?php declare(strict_types=1);


namespace Swoft\Rpc\Client\Contract;

/**
 * Class ExtenderInterface
 *
 * @since 2.0
 */
interface ExtenderInterface
{
    /**
     * @return array
     *
     * @example
     * [
     *     'key' => 'value',
     *     'key' => 'value'
     * ]
     */
    public function getExt(): array;
}