<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Unit;


use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use SwoftTest\Http\Server\Testing\MockHttpServer;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockHttpServer
     */
    protected $mockServer;

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function setUp()
    {
        $this->mockServer = BeanFactory::getBean(MockHttpServer::class);
    }
}