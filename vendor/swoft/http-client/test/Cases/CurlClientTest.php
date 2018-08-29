<?php

namespace SwoftTest\HttpClient;

use Swoft\App;
use Swoft\HttpClient\Client;
use Swoft\Http\Message\Testing\Base\Response;
use Swoft\HttpClient\Exception\RuntimeException;

/**
 * @uses      CoroutineClientTest
 * @version   2017-11-22
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class CurlClientTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function get()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $method = 'GET';

        // Http
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://www.swoft.org',
        ])->getResponse();
        $response->assertSuccessful()->assertSee('Swoft 官网');

        // Http - Magic call
        /** @var Response $response */
        $response = $client->get('', [
            'base_uri' => 'http://www.swoft.org',
        ])->getResponse();
        $response->assertSuccessful()->assertSee('Swoft 官网');

        // Https
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'https://www.swoft.org',
        ])->getResponse();
        $response->assertSuccessful()->assertSee('Swoft 官网');

        // TODO add redirect HTTPS support

        /** @var Response $response */
        $response = $client->request($method, '/?a=1', [
            'base_uri' => 'http://echo.swoft.org',
        ])->getResponse();
        $response->assertSuccessful();
        $this->assertJson($response->getBody()->getContents());
        $body = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('a=1', $body['uri']['query']);
    }

    /**
     * @test
     */
    public function postRawContent()
    {
        $client = new Client();
        $client->setAdapter('curl');

        /**
         * Post raw body
         */
        $body = 'raw';
        $method = 'POST';
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://echo.swoft.org',
            'body' => $body,
        ])->getResponse();
        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonFragment([
                'Host' => 'echo.swoft.org',
                'Content-Type' => 'text/plain',
            ])->assertJsonFragment([
                'uri' => [
                    'scheme' => 'http',
                    'userInfo' => '',
                    'host' => 'echo.swoft.org',
                    'port' => 80,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ])->assertJsonFragment([
                'method' => $method,
                'body' => $body,
            ]);
    }

    /**
     * @test
     */
    public function postFormParams()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $body = [
            'string' => 'value',
            'int' => 1,
            'boolean' => true,
            'float' => 1.2345,
            'array' => [
                '1',
                '2',
            ],
        ];
        $method = 'POST';
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://echo.swoft.org',
            'form_params' => $body,
        ])->getResponse();
        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonFragment([
                'Host' => 'echo.swoft.org',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->assertJsonFragment([
                'uri' => [
                    'scheme' => 'http',
                    'userInfo' => '',
                    'host' => 'echo.swoft.org',
                    'port' => 80,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ])->assertJsonFragment([
                'method' => $method,
                'body' => http_build_query($body),
            ]);
    }

    /**
     * @test
     */
    public function postJson()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $body = [
            'string' => 'value',
            'int' => 1,
            'boolean' => true,
            'float' => 1.2345,
            'array' => [
                '1',
                '2',
            ],
        ];
        $method = 'POST';
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://echo.swoft.org',
            'json' => $body,
        ])->getResponse();
        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonFragment([
                'Host' => 'echo.swoft.org',
                'Content-Type' => 'application/json',
            ])->assertJsonFragment([
                'uri' => [
                    'scheme' => 'http',
                    'userInfo' => '',
                    'host' => 'echo.swoft.org',
                    'port' => 80,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ])->assertJsonFragment([
                'method' => $method,
                'body' => json_encode($body),
            ]);
    }

    /**
     * @test
     */
    public function putJson()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $body = [
            'string' => 'value',
            'int' => 1,
            'boolean' => true,
            'float' => 1.2345,
            'array' => [
                '1',
                '2',
            ],
        ];
        $method = 'PUT';
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://echo.swoft.org',
            'json' => $body,
        ])->getResponse();
        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonFragment([
                'Host' => 'echo.swoft.org',
                'Content-Type' => 'application/json',
            ])->assertJsonFragment([
                'uri' => [
                    'scheme' => 'http',
                    'userInfo' => '',
                    'host' => 'echo.swoft.org',
                    'port' => 80,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ])->assertJsonFragment([
                'method' => $method,
                'body' => json_encode($body),
            ]);
    }

    /**
     * @test
     */
    public function deleteJson()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $body = [
            'string' => 'value',
            'int' => 1,
            'boolean' => true,
            'float' => 1.2345,
            'array' => [
                '1',
                '2',
            ],
        ];
        $method = 'DELETE';
        /** @var Response $response */
        $response = $client->request($method, '', [
            'base_uri' => 'http://echo.swoft.org',
            'json' => $body,
        ])->getResponse();
        $response->assertSuccessful()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonFragment([
                'Host' => 'echo.swoft.org',
                'Content-Type' => 'application/json',
            ])->assertJsonFragment([
                'uri' => [
                    'scheme' => 'http',
                    'userInfo' => '',
                    'host' => 'echo.swoft.org',
                    'port' => 80,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ])->assertJsonFragment([
                'method' => $method,
                'body' => json_encode($body),
            ]);
    }

    /**
     * @test
     * @requires extension curl
     */
    public function defaultUserAgent()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $expected = sprintf('Swoft/%s curl/%s PHP/%s', App::version(), \curl_version()['version'], PHP_VERSION);
        $this->assertEquals($expected, $client->getDefaultUserAgent());
    }

    /**
     * @test
     */
    public function exception()
    {
        $client = new Client();
        $client->setAdapter('curl');
        $method = 'GET';

        /** @var Response $response */
        $request = $client->request($method, '', [
            'base_uri' => 'http://www.swoft.org',
        ]);
        $request->getResponse();
        // getResponse twice
        try {
            $request->getResponse();
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
        }
    }

}
