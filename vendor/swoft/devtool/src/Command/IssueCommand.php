<?php declare(strict_types=1);

namespace Swoft\Devtool\Command;

use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Stdlib\Helper\Sys;

/**
 * There are some commands for application dev[by <cyan>devtool</cyan>]
 * @Command(coroutine=false)
 */
class IssueCommand
{
    private $issueUrl = 'https://github.com/swoft-cloud/swoft/issues?q=is%3Aissue+is%3Aopen+sort%3Aupdated-desc';

    /**
     * Open github issues page
     *
     * @CommandMapping()
     */
    public function open(): void
    {
        /*
        Mac：
        open 'https://swoft.org'

        Linux:
        x-www-browser 'https://swoft.org'

        Windows:
        cmd /c start https://swoft.org
         */
        if (Sys::isMac()) {
            $cmd = 'open';
        } elseif (Sys::isWin()) {
            $cmd = 'cmd /c start';
        } else {
            $cmd = 'x-www-browser';
        }

        Sys::execute($cmd . ' ' . $this->issueUrl);
    }

    /**
     * @CommandMapping()
     * @return int
     */
    public function create(): int
    {
        return 0;
    }

    /**
     * @CommandMapping()
     * @return int
     */
    public function report(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function search(): int
    {
        return 0;
    }
}
