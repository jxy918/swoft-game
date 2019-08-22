<?php declare(strict_types=1);

namespace Toolkit\CliTest;

use PHPUnit\Framework\TestCase;
use function explode;
use Toolkit\Cli\Flags;

/**
 * Class FlagsTest
 *
 * @package Toolkit\CliTest
 */
class FlagsTest extends TestCase
{
    public function testParseArgv(): void
    {
        $rawArgv = explode(' ', 'git:tag --only-tag -d ../view arg0');

        [$args, $sOpts, $lOpts] = Flags::parseArgv($rawArgv);

        $this->assertNotEmpty($args);
        $this->assertSame('git:tag', $args[0]);
        $this->assertSame('arg0', $args[1]);

        $this->assertSame('../view', $sOpts['d']);
        $this->assertTrue($lOpts['only-tag']);

        [$args, $opts] = Flags::parseArgv($rawArgv, ['mergeOpts' => true]);

        $this->assertNotEmpty($args);
        $this->assertSame('git:tag', $args[0]);
        $this->assertSame('arg0', $args[1]);

        $this->assertSame('../view', $opts['d']);
        $this->assertTrue($opts['only-tag']);
    }
}
