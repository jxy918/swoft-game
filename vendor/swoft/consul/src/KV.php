<?php declare(strict_types=1);


namespace Swoft\Consul;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Contract\KVInterface;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Consul\Helper\OptionsResolver;

/**
 * Class KV
 *
 * @since 2.0
 *
 * @Bean()
 */
class KV implements KVInterface
{
    /**
     * @Inject()
     *
     * @var Consul
     */
    private $consul;

    /**
     * Uri prefix
     *
     * @var string
     */
    private $prefix = '/v1/kv';

    /**
     * @param string $key
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function get(string $key, array $options = []): Response
    {
        $availableOptions = [
            'dc',
            'recurse',
            'keys',
            'separator',
            'raw',
            'stale',
            'consistent',
            'default'
        ];

        $params = array(
            'query' => OptionsResolver::resolve($options, $availableOptions),
        );

        return $this->consul->get($this->prefix . $key, $params);
    }

    /**
     * @param string $key
     * @param string $value
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function put(string $key, string $value, array $options = []): Response
    {
        $params = array(
            'body'  => (string)$value,
            'query' => OptionsResolver::resolve($options, ['dc', 'flags', 'cas', 'acquire', 'release']),
        );

        return $this->consul->put($this->prefix . $key, $params);
    }

    /**
     * @param string $key
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function delete(string $key, array $options = []): Response
    {
        $params = array(
            'query' => OptionsResolver::resolve($options, ['dc', 'recurse']),
        );

        return $this->consul->delete($this->prefix . $key, $params);
    }
}