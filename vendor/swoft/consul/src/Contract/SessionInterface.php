<?php declare(strict_types=1);


namespace Swoft\Consul\Contract;


use Swoft\Consul\Response;

/**
 * Class SessionInterface
 *
 * @since 2.0
 */
interface SessionInterface
{
    /**
     * @param array $body
     * @param array $options
     *
     * @return Response
     */
    public function create(array $body, array $options = []): Response;

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     */
    public function destroy(string $sessionId, array $options = []): Response;

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     */
    public function info(string $sessionId, array $options = []): Response;

    /**
     * @param string $node
     * @param array  $options
     *
     * @return Response
     */
    public function node(string $node, array $options = []): Response;

    /**
     * @param array $options
     *
     * @return Response
     */
    public function all(array $options = []): Response;

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     */
    public function renew(string $sessionId, array $options = []): Response;
}