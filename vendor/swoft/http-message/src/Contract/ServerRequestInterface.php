<?php declare(strict_types=1);

namespace Swoft\Http\Message\Contract;

use Swoole\Http\Request;

/**
 * Interface ServerRequestInterface
 * @since 2.0
 */
interface ServerRequestInterface extends \Psr\Http\Message\ServerRequestInterface
{
    /**
     * @see $_SERVER
     * @var array
     */
    public const DEFAULT_SERVER = [
        'server_protocol'      => 'HTTP/1.1',
        'remote_addr'          => '127.0.0.1',
        'request_method'       => 'GET',
        'request_uri'          => '/',
        'request_time'         => 0,
        'request_time_float'   => 0,
        'query_string'         => '',
        'server_addr'          => '127.0.0.1',
        'server_name'          => 'localhost',
        'server_port'          => 80,
        'script_name'          => '',
        'https'                => '',
        'http_host'            => 'localhost',
        'http_accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'http_accept_language' => 'en-US,en;q=0.8',
        'http_accept_charset'  => 'utf-8;q=0.7,*;q=0.3',
        'http_user_agent'      => 'Unknown',
    ];

    /**
     * @return Request
     */
    public function getCoRequest(): Request;
}
