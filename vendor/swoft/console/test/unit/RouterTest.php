<?php declare(strict_types=1);

namespace SwoftTest\Console\Unit;

use PHPUnit\Framework\TestCase;
use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class RouterTest
 */
class RouterTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testBasic(): void
    {
        $router = bean('cliRouter');
        $this->assertTrue($router->count() > 0);

        $router->setIdAliases(['run' => 'serve:run']);
        $this->assertNotEmpty($list = $router->getIdAliases());
        $this->assertSame('serve:run', $list['run']);
    }
}
