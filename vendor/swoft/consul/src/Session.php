<?php declare(strict_types=1);


namespace Swoft\Consul;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Contract\SessionInterface;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Consul\Helper\OptionsResolver;

/**
 * Class Session
 *
 * @since 2.0
 *
 * @Bean()
 */
class Session implements SessionInterface
{
    /**
     * @Inject()
     *
     * @var Consul
     */
    private $consul;

    /**
     * @param array $body
     * @param array $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function create(array $body = null, array $options = []): Response
    {
        $params = array(
            'body'  => $body,
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->put('/v1/session/create', $params);
    }

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function destroy(string $sessionId, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->put('/v1/session/destroy/' . $sessionId, $params);
    }

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function info(string $sessionId, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->get('/v1/session/info/' . $sessionId, $params);
    }

    /**
     * @param string $node
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function node(string $node, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->get('/v1/session/node/' . $node, $params);
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
    public function all(array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->get('/v1/session/list', $params);
    }

    /**
     * @param string $sessionId
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function renew(string $sessionId, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc']),
        );

        return $this->consul->put('/v1/session/renew/' . $sessionId, $params);
    }
}