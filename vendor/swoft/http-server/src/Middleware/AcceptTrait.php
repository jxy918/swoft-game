<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Http\Message\Server\Response;
use Swoft\Http\Server\AttributeEnum;

/**
 * Trait AcceptTrait
 * @package Swoft\Http\Server\Middleware
 */
trait AcceptTrait
{
    /**
     * Json format accept
     *
     * @var string
     */
    protected $acceptJson = 'application/json';

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \InvalidArgumentException
     */
    protected function handleAccept(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Only handle HTTP-Server Response
        if (!$response instanceof Response) {
            return $response;
        }

        // View(has been handled by ViewMiddleware)
        $data = $response->getAttribute(AttributeEnum::RESPONSE_ATTRIBUTE);
        if ($data === null) {
            return $response;
        }

        $accepts = $request->getHeader('accept');
        $currentAccept = \current($accepts);

        if (empty($currentAccept)) {
            if ($response->isArrayable($data)) {
                $response = $response->json($data);
                return $response;
            }

            return $response->raw((string)$data);
        }

        $isJson = $response->isMatchAccept($currentAccept, $this->acceptJson);
        $isArrayable = $response->isArrayable($data);

        if ($isJson || $isArrayable) {
            return $response->json($data);
        }

        if (!empty($data)) {
            return $response->raw((string)$data);
        }

        return $response;
    }

}
