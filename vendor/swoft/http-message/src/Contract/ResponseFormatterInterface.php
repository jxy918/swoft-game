<?php declare(strict_types=1);

namespace Swoft\Http\Message\Contract;

use Psr\Http\Message\ResponseInterface;
use Swoft\Http\Message\Response;

/**
 * Class ResponseFormatterInterface
 *
 * @since 2.0
 */
interface ResponseFormatterInterface
{
    /**
     * @param Response $response
     *
     * @return Response|ResponseInterface
     */
    public function format(Response $response): Response;
}
