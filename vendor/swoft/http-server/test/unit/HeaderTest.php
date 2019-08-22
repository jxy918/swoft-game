<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Unit;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class HeaderTest
 *
 * @since 2.0
 */
class HeaderTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testHeaderLines(): void
    {
        $headers = [
            'user-agent'   => 'curl/7.29.0',
            'host'         => '127.0.0.1:18306',
            'content-type' => 'application/json',
            'accept'       => '*/*',
            'id'           => 1024,
            'id2'          => 2048
        ];

        $response = $this->mockServer->request('POST', '/testHeader/headerLines', [], $headers);
        $response->assertEqualJson($headers);
    }
}