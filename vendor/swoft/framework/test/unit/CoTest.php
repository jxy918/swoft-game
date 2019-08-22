<?php declare(strict_types=1);


namespace SwoftTest\Unit;


use function count;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Context\Context;
use Swoole\Coroutine\Http\Client;

/**
 * Class CoTest
 *
 * @since 2.0
 */
class CoTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function tearDown()
    {
        Context::getWaitGroup()->wait();
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testMulti()
    {
        $requests = [
            'method'       => [$this, 'requestMethod'],
            'staticMethod' => "SwoftTest\Unit\CoTest::requestMehtodByStatic",
            'closure'      => function () {
                $cli = new Client('www.baidu.com', 80);
                $cli->get('/');
                $result = $cli->body;
                $cli->close();

                return $result;
            },
            'exception'    => [$this, 'exceptionMethod']
        ];

        $response = Co::multi($requests);

        $this->assertEquals(count($response), 3);
    }

    /**
     * @return mixed
     */
    public function requestMethod()
    {
        $cli = new Client('www.baidu.com', 80);
        $cli->get('/');
        $result = $cli->body;
        $cli->close();

        return $result;
    }

    /**
     * @throws Exception
     */
    public function exceptionMethod()
    {
        throw new Exception('xx exception');
    }

    /**
     * @return mixed
     */
    public static function requestMehtodByStatic()
    {
        $cli = new Client('www.baidu.com', 80);
        $cli->get('/');
        $result = $cli->body;
        $cli->close();

        return $result;
    }
}