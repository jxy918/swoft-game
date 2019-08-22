<?php declare(strict_types=1);

namespace SwoftTest\Console\Testing;

use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandArgument;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;

/**
 * This is an demo command of swoft console
 * @Command()
 *
 * The follow options is common for the group
 *
 * @CommandOption("comm1", short="a", desc="option description 1", mode=Command::OPT_REQUIRED)
 * @CommandOption(
 *     "comm2",
 *     short="b",
 *     desc="option description 2",
 *     mode=Command::OPT_REQUIRED
 * )
 */
class DemoCommand
{
    /**
     * this is an test command1 in demo group
     *
     * run:
     *  bin/swoft demo:index
     *  bin/swoft demo:sub1
     * @CommandMapping("index", alias="sub")
     *
     * The follow options is for the command
     *
     * @CommandOption("opt1", short="c", desc="option description 1")
     * @CommandOption("opt2", short="d", desc="option description 2")
     *
     * @CommandArgument("arg1", desc="arguemnt description 1", mode=Command::ARG_REQUIRED)
     * @CommandArgument("arg2", desc="arguemnt description 2", mode=Command::ARG_IS_ARRAY)
     *
     * @return string
     */
    public function sub1(): string
    {
        // do something...
        return 'demo:sub1';
    }

    /**
     * this is an test command2 in demo group
     *
     * run:
     *  bin/swoft demo:sub2
     * @CommandMapping()
     */
    public function sub2(): void
    {
        // do something...
    }
}
