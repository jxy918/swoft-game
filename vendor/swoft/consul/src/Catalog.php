<?php declare(strict_types=1);


namespace Swoft\Consul;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Contract\CatalogInterface;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Consul\Helper\OptionsResolver;

/**
 * Class Catalog
 *
 * @since 2.0
 *
 * @Bean()
 */
class Catalog implements CatalogInterface
{
    /**
     * @Inject()
     *
     * @var Consul
     */
    private $consul;

    /**
     * @param array $node
     *
     * @return Response
     *
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function register(array $node): Response
    {
        $params = [
            'body' => $node,
        ];

        return $this->consul->put('/v1/catalog/register', $params);
    }

    /**
     * @param array $node
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function deregister(array $node): Response
    {
        $params = [
            'body' => $node,
        ];

        return $this->consul->put('/v1/catalog/deregister', $params);
    }

    /**
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function datacenters(): Response
    {
        return $this->consul->get('/v1/catalog/datacenters');
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
    public function nodes(array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->consul->get('/v1/catalog/nodes', $params);
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
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->consul->get('/v1/catalog/node/' . $node, $params);
    }

    /**
     * @param array $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function services(array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc']),
        ];

        return $this->consul->get('/v1/catalog/services', $params);
    }

    /**
     * @param string $service
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function service(string $service, array $options = []): Response
    {
        $params = [
            'query' => OptionsResolver::resolve($options, ['dc', 'tag']),
        ];

        return $this->consul->get('/v1/catalog/service/' . $service, $params);
    }
}