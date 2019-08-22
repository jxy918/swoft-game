<?php declare(strict_types=1);


namespace Swoft\Consul;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Contract\AgentInterface;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Consul\Helper\OptionsResolver;

/**
 * Class Agent
 *
 * @since 2.0
 *
 * @Bean()
 */
class Agent implements AgentInterface
{
    /**
     * @Inject()
     *
     * @var Consul
     */
    private $consul;

    /**
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function checks(): Response
    {
        return $this->consul->get('/v1/agent/checks');
    }

    /**
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function services(): Response
    {
        return $this->consul->get('/v1/agent/services');
    }

    /**
     * @param array $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function members(array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['wan']),
        ];

        return $this->consul->get('/v1/agent/members', $params);
    }

    /**
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function self(): Response
    {
        return $this->consul->get('/v1/agent/self');
    }

    /**
     * @param string $address
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function join(string $address, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['wan']),
        );

        return $this->consul->get('/v1/agent/join/' . $address, $params);
    }

    /**
     * @param string $node
     *
     * @return Response
     * @throws Exception\ClientException
     * @throws Exception\ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function forceLeave(string $node): Response
    {
        return $this->consul->get('/v1/agent/force-leave/' . $node);
    }

    /**
     * @param array $check
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function registerCheck(array $check): Response
    {
        $params = [
            'body' => $check,
        ];

        return $this->consul->put('/v1/agent/check/register', $params);
    }

    /**
     * @param string $checkId
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function deregisterCheck(string $checkId): Response
    {
        return $this->consul->put('/v1/agent/check/deregister/' . $checkId);
    }

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function passCheck(string $checkId, array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->consul->put('/v1/agent/check/pass/' . $checkId, $params);
    }

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function warnCheck(string $checkId, array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->consul->put('/v1/agent/check/warn/' . $checkId, $params);
    }

    /**
     * @param string $checkId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function failCheck(string $checkId, array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['note']),
        ];

        return $this->consul->put('/v1/agent/check/fail/' . $checkId, $params);
    }

    /**
     * @param string $service
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function registerService(array $service): Response
    {
        $params = [
            'body' => $service,
        ];

        return $this->consul->put('/v1/agent/service/register', $params);
    }

    /**
     * @param string $serviceId
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function deregisterService(string $serviceId): Response
    {
        return $this->consul->put('/v1/agent/service/deregister/' . $serviceId);
    }
}