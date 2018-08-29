<?php

namespace Swoft\Rpc\Client\Service;

/**
 * The interface of service connect
 */
interface ServiceConnectInterface
{
    public function reConnect();

    /**
     * @param string $data
     * @return bool
     */
    public function send(string $data): bool;

    /**
     * @return string
     */
    public function recv(): string;
}
