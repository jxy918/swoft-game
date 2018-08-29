<?php

namespace Swoft\Rpc\Packer;

/**
 * Packer Interface
 */
interface PackerInterface
{
    /**
     * Pack data
     *
     * @param mixed $data
     * @return mixed
     */
    public function pack($data);

    /**
     * Unpack data
     *
     * @param mixed $data
     * @return mixed
     */
    public function unpack($data);
}
