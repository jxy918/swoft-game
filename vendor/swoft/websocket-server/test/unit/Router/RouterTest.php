<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Unit\Router;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\WebSocket\Server\Router\Router;
use SwoftTest\Testing\Concern\CommonTestAssertTrait;
use function bean;

/**
 * Class RouterTest
 *
 * @since 2.0
 */
class RouterTest extends TestCase
{
    use CommonTestAssertTrait;

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testRouter(): void
    {
        /** @var Router $router */
        $router = bean('wsRouter');

        $this->assertTrue($router->hasModule('/ws-test/chat'));
        $this->assertGreaterThan(0, $router->getCounter());
        $this->assertGreaterThan(0, $router->getModuleCount());

        $this->assertNotEmpty($router->getModules());
        $this->assertNotEmpty($router->getCommands());

        [$status, $info] = $router->matchCommand('/ws-test/chat', 'chat.send');
        $this->assertSame(Router::FOUND, $status);
        $this->assertNotEmpty($info);
        $this->assertArrayHasKey('cmdId', $info);
        $this->assertArrayHasKey('opcode', $info);
        $this->assertArrayHasKey('handler', $info);
        $this->assertSame('chat.send', $info['cmdId']);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testAddModule(): void
    {
        /** @var Router $router */
        $router = bean('wsRouter');

        $this->assertFalse($router->hasModule('/new-test'));
        // add new module
        $router->addModule('/new-test', [
            'name' => 'test'
        ]);
        $this->assertTrue($router->hasModule('/ws-test/chat'));

        $info = $router->match('/new-test');
        $this->assertIsArray($info);
        $this->assertNotEmpty($info);
        $this->assertSame('test', $info['name']);
        $this->assertSame('/new-test', $info['path']);

        $info = $router->match('/not-exist');
        $this->assertEmpty($info);
        $this->assertIsArray($info);
    }

    public function testDynamicRoute(): void
    {
        /** @var Router $router */
        $router = new Router();
        $router->addModule('/page/{name}', ['name' => 'test']);

        $info = $router->match('/page/about');
        $this->assertNotEmpty($info);

        // limit by regex
        $router->addModule('/users/{id}', [
            'params' => [
                'id' => '\d+'
            ]
        ]);

        $info = $router->match('/users/12');
        $this->assertNotEmpty($info);
        $this->assertArrayHasKey('routeParams', $info);
        $this->assertArrayHasKey('id', $info['routeParams']);
        $this->assertNotEmpty($info['routeParams']['id']);
        $this->assertSame('12', $info['routeParams']['id']);

        $info = $router->match('/users/tom');
        $this->assertEmpty($info);
    }

    public function testDisableModule(): void
    {
        /** @var Router $router */
        $router = new Router();
        $router->setDisabledModules(['/chat']);

        $router->addModule('/chat', ['name' => 'test']);
        $this->assertSame(0, $router->getCounter());
        $this->assertEmpty($router->getModules());
        $this->assertEmpty($router->getCommands());

        $info = $router->match('/chat');
        $this->assertEmpty($info);
    }
}
