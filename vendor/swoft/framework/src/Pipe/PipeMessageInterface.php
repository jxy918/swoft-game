<?php

namespace Swoft\Pipe;

/**
 * The pipe message interface
 */
interface PipeMessageInterface
{
    /**
     * @param string $type
     * @param array  $data
     *
     * @return string
     */
    public function pack(string $type, array $data): string;

    /**
     * @param string $message
     *
     * @return array
     */
    public function unpack(string $message): array;
}