<?php declare(strict_types=1);


namespace Swoft\Consul\Contract;

use Swoft\Consul\Response;

/**
 * Class AgentInterface
 *
 * @since 2.0
 */
interface AgentInterface
{
    /**
     * @return Response
     */
    public function checks();

    /**
     * @return Response
     */
    public function services(): Response;

    /**
     * @param array $options
     *
     * @return Response
     */
    public function members(array $options = []): Response;

    /**
     * @return Response
     */
    public function self(): Response;

    /**
     * @param string $address
     * @param array  $options
     *
     * @return Response
     */
    public function join(string $address, array $options = []): Response;

    /**
     * @param string $node
     *
     * @return Response
     */
    public function forceLeave(string $node): Response;

    /**
     * @param array $check
     *
     * @return Response
     */
    public function registerCheck(array $check): Response;

    /**
     * @param string $checkId
     *
     * @return Response
     */
    public function deregisterCheck(string $checkId): Response;

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     */
    public function passCheck(string $checkId, array $options = []): Response;

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     */
    public function warnCheck(string $checkId, array $options = []): Response;

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     */
    public function failCheck(string $checkId, array $options = []): Response;

    /**
     * @param array $service
     *
     * @return Response
     */
    public function registerService(array $service): Response;

    /**
     * @param string $serviceId
     *
     * @return Response
     */
    public function deregisterService(string $serviceId): Response;
}