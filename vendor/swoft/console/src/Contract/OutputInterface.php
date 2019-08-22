<?php declare(strict_types=1);

namespace Swoft\Console\Contract;

/**
 * Interface OutputInterface
 * @since 1.0
 */
interface OutputInterface
{
    /**
     * Write a message to standard output stream.
     * @param mixed       $messages Output message
     * @param bool        $nl true 会添加换行符 false 原样输出，不添加换行符
     * @param int|boolean $quit If is int, setting it is exit code.
     * @param array       $opts
     * @return int
     */
    public function write($messages, $nl = true, $quit = false, array $opts = []): int;
}
