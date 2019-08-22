<?php declare(strict_types=1);

namespace SwoftTest\Console\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Application;
use function bean;
use function input;

/**
 * Class ApplicationTest
 */
class ApplicationTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testBasic(): void
    {
        $app = bean('cliApp');

        $this->assertNotEmpty($app);
        $this->assertInstanceOf(Application::class, $app);

        $app->setName('cli app');
        $this->assertSame('cli app', $app->getName());

        $app->setVersion('1.0.0');
        $this->assertSame('1.0.0', $app->getVersion());

        $app->setDescription('cli desc');
        $this->assertSame('Cli desc', $app->getDescription());

        $this->assertEmpty($app->getCommentsVars());
        $app->setCommentsVars(['newKey' => 'new val']);
        $this->assertIsArray($vars = $app->getCommentsVars());
        $this->assertArrayHasKey('newKey', $vars);
        $this->assertArrayNotHasKey('workDir', $vars);
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testRun(): void
    {
        $app = bean('cliApp');

        $input = input();
        $input->setLOpts([
            'comm1' => 'v1',
            'comm2' => 'v2',
        ]);
        $input->setArgs(['a1']);
        $input->setCommand('demo:sub');

        $app->run();

        $this->assertIsArray($vars = $app->getCommentsVars());
    }
}
