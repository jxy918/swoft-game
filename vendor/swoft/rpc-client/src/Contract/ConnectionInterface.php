<?php declare(strict_types=1);


namespace Swoft\Rpc\Client\Contract;

/**
 * Class ConnectionInterface
 *
 * @since 2.0
 */
interface ConnectionInterface
{
    /**
     * @param string $data
     *
     * @return bool
     */
    public function send(string $data): bool;

    /**
     * @return string|bool
     */
    public function recv();
}