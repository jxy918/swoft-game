<?php

namespace Swoft\HttpClient;

use Psr\Http\Message\UriInterface;
use Swoft\App;
use Swoft\Core\Coroutine;
use Swoft\Http\Message\Base\Request;
use Swoft\Http\Message\Uri\Uri;

/**
 * Http client
 * @method HttpResult get(string | UriInterface $uri, array $options = [])
 * @method HttpResult head(string | UriInterface $uri, array $options = [])
 * @method HttpResult put(string | UriInterface $uri, array $options = [])
 * @method HttpResult post(string | UriInterface $uri, array $options = [])
 * @method HttpResult patch(string | UriInterface $uri, array $options = [])
 * @method HttpResult delete(string | UriInterface $uri, array $options = [])
 */
class Client
{

    /**
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $adapters
        = [
            'php' => Adapter\CurlAdapter::class,
            'curl' => Adapter\CurlAdapter::class,
            'co' => Adapter\CoroutineAdapter::class,
            'coroutine' => Adapter\CoroutineAdapter::class,
            'swoole' => Adapter\CoroutineAdapter::class,
        ];

    /**
     * @var array
     * @example [
     *      'adapter' => 'curl',
     *      'base_uri' => 'http://www.swoft.org',
     * ]
     */
    protected $configs = [];

    /**
     * @var string
     */
    protected $defaultUserAgent = '';

    /**
     * Client constructor.
     *
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        // Convert the base_uri to a UriInterface
        if (! empty($config['base_uri'])) {
            $config['base_uri'] = $this->convertBaseUri($config['base_uri']);
        }

        // Set specified adapter if config adapter
        if (! empty($config['adapter'])) {
            $this->setAdapter($config['adapter']);
        }

        $this->configureDefaults($config);
    }

    /**
     * Send a Http request
     *
     * @param string $method
     * @param string|UriInterface $uri
     * @param array $options
     * @return HttpResultInterface
     * @throws \InvalidArgumentException
     */
    public function request(string $method, $uri, array $options = []): HttpResultInterface
    {
        $options = $this->prepareDefaults($options);
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? null;
        $version = $options['version'] ?? '1.1';
        // Merge the URI into the base URI.
        $uri = $this->buildUri($uri, $options);
        $profileKey = 'http.' . (string)$uri;
        App::profileStart($profileKey);
        $request = new Request($method, $uri, $headers, $body, $version);
        $adapter = $this->getAdapter();
        $this->setDefaultUserAgent();
        $result = $adapter->request($request, $options);
        App::profileEnd($profileKey);
        return $result;
    }

    /**
     * @param string $method
     * @param array $args
     * @return HttpResult
     * @throws \InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (\count($args) < 1) {
            throw new \InvalidArgumentException('Magic request methods require a URI and optional options array');
        }

        $uri = $args[0];
        $options = $args[1] ?? [];
        return $this->request($method, $uri, $options);
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    protected function buildUri($uri, array $options): UriInterface
    {
        if (! $uri instanceof UriInterface) {
            $uri = new Uri($uri);
        }
        if (isset($options['base_uri'])) {
            $baseUri = $this->convertBaseUri($options['base_uri']);
            $uri = $this->resolve($baseUri, $uri);
        }

        return ! $uri->getScheme() && ! $uri->getHost() ? $uri->withScheme('http') : $uri;
    }

    /**
     * @param UriInterface $base
     * @param UriInterface $rel
     * @return UriInterface
     * @throws \InvalidArgumentException
     */
    protected function resolve(UriInterface $base, UriInterface $rel): UriInterface
    {
        if ((string)$rel === '') {
            return $base;
        }
        if ($rel->getScheme() !== '') {
            return $rel->withScheme($this->removeDotSegments($rel->getPath()));
        }
        if ($rel->getAuthority() !== '') {
            $targetAuthority = $rel->getAuthority();
            $targetPath = $this->removeDotSegments($rel->getPath());
            $targetQuery = $rel->getQuery();
        } else {
            $targetAuthority = $base->getAuthority();
            if ($rel->getPath() === '') {
                $targetPath = $base->getPath();
                $targetQuery = $rel->getQuery() !== '' ? $rel->getQuery() : $base->getQuery();
            } else {
                if ($rel->getPath()[0] === '/') {
                    $targetPath = $rel->getPath();
                } else {
                    if ($targetAuthority !== '' && $base->getPath() === '') {
                        $targetPath = '/' . $rel->getPath();
                    } else {
                        $lastSlashPos = strrpos($base->getPath(), '/');
                        if ($lastSlashPos === false) {
                            $targetPath = $rel->getPath();
                        } else {
                            $targetPath = substr($base->getPath(), 0, $lastSlashPos + 1) . $rel->getPath();
                        }
                    }
                }
                $targetPath = $this->removeDotSegments($targetPath);
                $targetQuery = $rel->getQuery();
            }
        }
        return new Uri(Uri::composeComponents($base->getScheme(), $targetAuthority, $targetPath, $targetQuery, $rel->getFragment()));
    }

    /**
     * Removes dot segments from a path and returns the new path.
     *
     * @param string $path
     * @return string
     * @link http://tools.ietf.org/html/rfc3986#section-5.2.4
     */
    protected function removeDotSegments($path): string
    {
        if ($path === '' || $path === '/') {
            return $path;
        }

        $results = [];
        $segments = explode('/', $path);
        $segment = null;
        foreach ($segments as $segment) {
            if ($segment === '..') {
                array_pop($results);
            } elseif ($segment !== '.') {
                $results[] = $segment;
            }
        }

        $newPath = implode('/', $results);

        if ($path[0] === '/' && (! isset($newPath[0]) || $newPath[0] !== '/')) {
            // Re-add the leading slash if necessary for cases like "/.."
            $newPath = '/' . $newPath;
        } elseif ($newPath !== '' && ($segment === '.' || $segment === '..')) {
            // Add the trailing slash if necessary
            // If newPath is not empty, then $segment must be set and is the last segment from the foreach
            $newPath .= '/';
        }

        return $newPath;
    }

    /**
     * @return Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function getAdapter(): Adapter\AdapterInterface
    {
        if (! $this->adapter instanceof Adapter\AdapterInterface) {
            if (App::isCoContext()) {
                $this->setAdapter(new Adapter\CoroutineAdapter());
            } else {
                $this->setAdapter(new Adapter\CurlAdapter());
            }
        }
        return $this->adapter;
    }

    /**
     * @param Adapter\AdapterInterface|string $adapter
     * @return Client
     * @throws \InvalidArgumentException
     */
    public function setAdapter($adapter): Client
    {
        if (\is_string($adapter)) {
            $adapter = strtolower($adapter);
            if (!empty($this->adapters[$adapter]) && class_exists($this->adapters[$adapter])) {
                $adapterClass = $this->adapters[$adapter];
                $adapterInstance = new $adapterClass();
                if ($adapterInstance instanceof Adapter\AdapterInterface) {
                    $adapter = $adapterInstance;
                }
            }
        }
        if (! $adapter instanceof Adapter\AdapterInterface) {
            throw new \InvalidArgumentException('Invalid http client adapter');
        }
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param string|UriInterface $baseUri
     * @return UriInterface|Uri
     */
    protected function convertBaseUri($baseUri)
    {
        $baseUri = $baseUri instanceof UriInterface ? $baseUri : new Uri($baseUri);
        return $baseUri;
    }

    /**
     * Configures the default options for a client.
     *
     * @param array $config
     */
    protected function configureDefaults(array $config)
    {
        $defaults = [
            'http_errors' => true,
            'decode_content' => true,
            'verify' => true,
        ];

        // TODO finish defaults config feature

        $this->configs = array_replace($defaults, $config);

        // TODO add cookies settings after session and cookies feature finished
    }

    /**
     * Merges default options into the array.
     *
     * @param array $options Options to modify by reference
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function prepareDefaults($options): array
    {
        $defaults = $this->configs;

        if (! empty($defaults['headers'])) {
            // Default headers are only added if they are not present.
            $defaults['_headers'] = $defaults['headers'];
            unset($defaults['headers']);
        }

        // Special handling for headers is required as they are added as
        if (array_key_exists('headers', $options)) {
            // Allows default headers to be unset.
            if ($options['headers'] === null) {
                $defaults['_headers'] = null;
                unset($options['headers']);
            } elseif (! \is_array($options['headers'])) {
                throw new \InvalidArgumentException('headers must be an array');
            }
        }

        // Shallow merge defaults underneath options.
        $result = array_replace($defaults, $options);

        // Remove null values.
        foreach ($result as $k => $v) {
            if ($v === null) {
                unset($result[$k]);
            }
        }

        return $result;
    }

    /**
     * Get the default User-Agent string
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getDefaultUserAgent(): string
    {
        if (! $this->defaultUserAgent) {
            $currentMethodName = __FUNCTION__;
            $isAdapterUserAgent = method_exists($this->getAdapter(), $currentMethodName);
            if ($isAdapterUserAgent) {
                $this->defaultUserAgent = $this->getAdapter()->$currentMethodName();
            } else {
                $defaultAgent = 'Swoft/' . App::version();
                if (! Coroutine::isSupportCoroutine() && \extension_loaded('curl') && \function_exists('curl_version')) {
                    $defaultAgent .= ' curl/' . \curl_version()['version'];
                }
                $defaultAgent .= ' PHP/' . PHP_VERSION;
                $this->defaultUserAgent = $defaultAgent;
            }
        }

        return $this->defaultUserAgent;
    }

    /**
     * Set default User-Agent to Client.
     *
     * @important Notice that this method should always
     * use after setAdapter() method, or else getDefaultUserAgent()
     * and getAdapter() methods will automated get an Adapter,
     * probably is not the adapter you wanted.
     * @return void
     * @throws \InvalidArgumentException
     */
    private function setDefaultUserAgent()
    {
        // Add the default user-agent header.
        if (! isset($this->configs['headers'])) {
            $this->configs['headers'] = ['User-Agent' => $this->getDefaultUserAgent()];
        } else {
            // Add the User-Agent header if one was not already set.
            foreach (array_keys($this->configs['headers']) as $name) {
                if (strtolower($name) === 'user-agent') {
                    return;
                }
            }
            $this->configs['headers']['User-Agent'] = $this->getDefaultUserAgent();
        }
    }

    /**
     * @return array
     */
    public function getAdapters(): array
    {
        return $this->adapters;
    }

    /**
     * @param array $adapters
     * @return $this
     */
    public function setAdapters(array $adapters): self
    {
        $this->adapters = $adapters;
        return $this;
    }

    /**
     * @param string $key
     * @param string $class
     * @return $this
     */
    public function addAdapter(string $key, string $class): self
    {
        $this->adapters[$key] = $class;
        return $this;
    }

}
