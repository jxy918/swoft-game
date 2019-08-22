<?php declare(strict_types=1);


namespace Swoft\Consul\Contract;

use Swoft\Consul\Response;

/**
 * Class KVInterface
 *
 * @since 2.0
 */
interface KVInterface
{
    /**
     * @param string $key
     * @param array  $options
     *
     * @return Response
     */
    public function get(string $key, array $options = []): Response;

    /**
     * @param string $key
     * @param string $value
     * @param array  $options
     *
     * @return Response
     */
    public function put(string $key, string $value, array $options = []): Response;

    /**
     * @param string $key
     * @param array  $options
     *
     * @return Response
     */
    public function delete(string $key, array $options = []): Response;
}