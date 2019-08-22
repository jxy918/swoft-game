<?php declare(strict_types=1);


namespace Swoft\Server\Contract;


use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class RequestInterface
 *
 * @since 2.0
 */
interface RequestInterface
{
    /**
     * Request event
     *
     * @param Request  $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response): void;
}