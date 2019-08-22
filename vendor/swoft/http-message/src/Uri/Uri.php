<?php declare(strict_types=1);

namespace Swoft\Http\Message\Uri;

use function explode;
use InvalidArgumentException;
use function parse_url;
use function preg_match;
use function preg_replace_callback;
use Psr\Http\Message\UriInterface;
use function rawurlencode;
use ReflectionException;
use function strpos;
use function strtolower;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class Uri
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class Uri implements UriInterface
{
    use PrototypeTrait;

    /**
     * Absolute http and https URIs require a host per RFC 7230 Section 2.7
     * but in generic URIs the host can be empty. So for http(s) URIs
     * we apply this default host when no host is given yet to form a
     * valid URI.
     */
    public const HTTP_DEFAULT_HOST = 'localhost';

    /**
     * Valid schemes
     */
    private const VALID_SCHEME = [
        ''      => 1,
        'https' => 1,
        'http'  => 1,
        'ws'    => 1,
        'wss'   => 1,
    ];

    /**
     * Default ports
     *
     * @var array
     */
    private const DEFAULT_PORTS = [
        'http'   => 80,
        'https'  => 443,
        'ftp'    => 21,
        'gopher' => 70,
        'nntp'   => 119,
        'news'   => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap'   => 143,
        'pop'    => 110,
        'ldap'   => 389,
    ];

    /**
     * Default uri params
     */
    private const DEFAULT_PARAMS = [
        'scheme'   => 'http',
        'host'     => 'localhost', // 80
        'user'     => '',
        'pass'     => '',
        'path'     => '/',
        'query'    => '',
        'fragment' => '',
    ];

    /**
     * @var string
     */
    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';

    /**
     * @var string
     */
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    /**
     * @var array
     */
    private static $replaceQuery = ['=' => '%3D', '&' => '%26'];

    /**
     * Uri scheme
     *
     * @var string
     */
    private $scheme = '';

    /**
     * user info.
     *
     * @var string
     */
    private $userInfo = '';

    /**
     * string Uri host.
     *
     * @var string
     */
    private $host = '';

    /**
     * Uri port.
     *
     * @var int|null
     */
    private $port;

    /**
     * Uri path.
     *
     * @var string
     */
    private $path = '';

    /**
     * Uri query string.
     *
     * @var string
     */
    private $query = '';

    /**
     * Uri fragment.
     *
     * @var string
     */
    private $fragment = '';

    /**
     * Storage some params for after use.
     * @var array
     * [
     *  host  => '', // it's from headers
     *  https => '',
     *  path  => '',
     *  query => '',
     *  http_host => '',
     *  server_name => '',
     *  server_addr => '',
     *  server_port => '',
     * ]
     */
    private $params = [];

    /**
     * Create Url replace for constructor
     *
     * @param string $uri
     * @param array  $params
     *
     * @return Uri
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(string $uri = '', array $params = []): self
    {
        /** @var Uri $instance */
        $instance = self::__instance();

        // Save some params
        $instance->params = $params;

        // Weak type check to also accept null until we can add scalar type hints
        if ($uri === '') {
            return $instance;
        }

        // If params is empty, padding defaults data
        if (!$params) {
            $instance->params = self::DEFAULT_PARAMS;
        }

        $parts = parse_url($uri);
        if ($parts === false) {
            throw new InvalidArgumentException("Unable to parse URI: $uri");
        }

        $instance->applyParts($parts);

        return $instance;
    }

    /**
     * @inheritdoc
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme(): string
    {
        // Init on first get
        if (!$this->scheme) {
            if ($https = $this->params['https'] ?? '') {
                $this->scheme = $https !== 'off' ? 'https' : 'http';
            } else {
                $this->scheme = 'http';
            }
        }

        return $this->scheme;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string
    {
        $authority = $this->getHost();
        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->getPort() !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }

    /**
     * @inheritdoc
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    /**
     * @inheritdoc
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost(): string
    {
        // Init on first get
        if ('' === $this->host) {
            $this->parseHostPort();
        }

        return $this->host;
    }

    /**
     * @inheritdoc
     *
     * @return null|int The URI port.
     */
    public function getPort(): ?int
    {
        // Init on first get
        if (null === $this->port) {
            $this->parseHostPort();
        }

        return $this->port;
    }

    /**
     * parse host port from $params. only use for swoft framework
     */
    private function parseHostPort(): void
    {
        if (!isset($this->params['http_host'])) {
            return;
        }

        if ($host = $this->params['http_host']) {
            $hostParts  = explode(':', $host, 2);
            $this->host = strtolower($hostParts[0]);

            if (isset($hostParts[1])) {
                $this->port = $this->filterPort($hostParts[1]);
                return;
            }
        } elseif ($host = $this->params['server_name'] ?? '') {
            $this->host = strtolower($host);
        } elseif ($host = $this->params['server_addr'] ?? '') {
            $this->host = strtolower($host);
        } elseif ($headerHost = $this->params['host'] ?? '') {
            $hostParts  = explode(':', $headerHost, 2);
            $this->host = strtolower($hostParts[0]);

            if (isset($hostParts[1]) && $hostParts[1] !== '80') {
                $this->port = $this->filterPort($hostParts[1]);
                return;
            }
        }

        if (!$this->port && !empty($this->params['server_port'])) {
            $this->port = $this->filterPort($this->params['server_port']);
        }
    }

    /**
     * @inheritdoc
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath(): string
    {
        // Init on get
        if ('' === $this->path) {
            // $this->path = $this->params['path'];
            $this->path = $this->filterPath($this->params['path']);
        }

        return $this->path;
    }

    /**
     * @inheritdoc
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery(): string
    {
        // Init on get
        if ('' === $this->query) {
            // $this->query = $this->params['query'];
            $this->query = $this->filterQueryAndFragment($this->params['query']);
        }

        return $this->query;
    }

    /**
     * @inheritdoc
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @inheritdoc
     *
     * @return static A new instance with the specified scheme.
     * @throws InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $scheme = $this->filterScheme($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }

        $clone = clone $this;

        $clone->scheme = $scheme;
        $clone->removeDefaultPort();
        $clone->validateState();
        return $clone;
    }

    /**
     * Return an instance with the specified user information.
     *
     * @inheritdoc
     *
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if ($password !== '') {
            $info .= ':' . $password;
        }

        if ($this->userInfo === $info) {
            return $this;
        }

        $clone = clone $this;

        $clone->userInfo = $user;
        $clone->validateState();
        return $clone;
    }

    /**
     * Return an instance with the specified host.
     *
     * @inheritdoc
     *
     * @return static A new instance with the specified host.
     * @throws InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $host = $this->filterHost($host);
        if ($this->host === $host) {
            return $this;
        }

        $clone = clone $this;

        $clone->host = $host;
        $clone->validateState();
        return $clone;
    }

    /**
     * Return an instance with the specified port.
     *
     * @inheritdoc
     *
     * @return static A new instance with the specified port.
     * @throws InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $port = $this->filterPort($port);
        if ($this->port === $port) {
            return $this;
        }

        $clone = clone $this;

        $clone->port = $port;
        $clone->validateState();
        return $clone;
    }

    /**
     * Return an instance with the specified path.
     *
     * @inheritdoc
     *
     * @param string $path The path to use with the new instance.
     *
     * @return static A new instance with the specified path.
     * @throws InvalidArgumentException for invalid paths.
     */
    public function withPath($path): self
    {
        $path = $this->filterPath($path);
        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;

        $clone->path = $path;
        $clone->validateState();
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @return static A new instance with the specified query string.
     * @throws InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        $query = $this->filterQueryAndFragment($query);
        if ($this->query === $query) {
            return $this;
        }

        $clone = clone $this;

        $clone->query = $query;
        return $clone;
    }

    /**
     * Creates a new URI with a specific query string value.
     * Any existing query string values that exactly match the provided key are
     * removed and replaced with the given key value pair.
     * A value of null will set the query string key without a value, e.g. "key"
     * instead of "key=value".
     *
     * @param UriInterface $uri   URI to use as a base.
     * @param string       $key   Key to set.
     * @param string|null  $value Value to set
     *
     * @return UriInterface
     */
    public static function withQueryValue(UriInterface $uri, $key, $value): UriInterface
    {
        $result   = self::getFilteredQueryString($uri, [$key]);
        $result[] = self::generateQueryString($key, $value);

        return $uri->withQuery(implode('&', $result));
    }

    /**
     * @inheritdoc
     *
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        $fragment = $this->filterQueryAndFragment($fragment);
        if ($this->fragment === $fragment) {
            return $this;
        }

        $clone = clone $this;

        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * @inheritdoc
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        return self::composeComponents(
            $this->scheme,
            $this->getAuthority(),
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * Composes a URI reference string from its various components.
     * Usually this method does not need to be called manually but instead is used indirectly via
     * `Psr\Http\Message\UriInterface::__toString`.
     * PSR-7 UriInterface treats an empty component the same as a missing component as
     * getQuery(), getFragment() etc. always return a string. This explains the slight
     * difference to RFC 3986 Section 5.3.
     * Another adjustment is that the authority separator is added even when the authority is missing/empty
     * for the "file" scheme. This is because PHP stream functions like `file_get_contents` only work with
     * `file:///myfile` but not with `file:/myfile` although they are equivalent according to RFC 3986. But
     * `file:///` is the more common syntax for the file scheme anyway (Chrome for example redirects to
     * that format).
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     *
     * @return string
     * @link https://tools.ietf.org/html/rfc3986#section-5.3
     */
    public static function composeComponents(
        string $scheme,
        string $authority,
        string $path,
        string $query,
        $fragment
    ): string {
        $uri = '';
        // weak type checks to also accept null until we can add scalar type hints
        if ($scheme !== '') {
            $uri .= $scheme . ':';
        }

        if ($authority !== '' || $scheme === 'file') {
            $uri .= '//' . $authority;
        }

        $uri .= $path;
        if ($query !== '') {
            $uri .= '?' . $query;
        }

        if ($fragment !== '') {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }

    /**
     * Apply parse_url parts to a URI.
     *
     * @param array $parts Array of parse_url parts to apply.
     */
    private function applyParts(array $parts): void
    {
        $this->scheme   = isset($parts['scheme']) ? $this->filterScheme($parts['scheme']) : '';
        $this->userInfo = isset($parts['user']) ? $this->filterUserInfoComponent($parts['user']) : '';
        $this->host     = isset($parts['host']) ? $this->filterHost($parts['host']) : '';
        $this->port     = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
        $this->path     = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
        $this->query    = isset($parts['query']) ? $this->filterQueryAndFragment($parts['query']) : '';
        $this->fragment = isset($parts['fragment']) ? $this->filterQueryAndFragment($parts['fragment']) : '';

        if (isset($parts['pass'])) {
            $this->userInfo .= ':' . $this->filterUserInfoComponent($parts['pass']);
        }

        $this->removeDefaultPort();
    }

    /**
     * @param string $scheme
     *
     * @return string
     *
     * @throws InvalidArgumentException If the scheme is invalid.
     */
    private function filterScheme(string $scheme): string
    {
        return $scheme ? strtolower($scheme) : 'http';
    }

    /**
     * @param string $component
     *
     * @return string
     *
     * @throws InvalidArgumentException If the user info is invalid.
     */
    private function filterUserInfoComponent(string $component): string
    {
        if (!$component) {
            return $component;
        }

        return preg_replace_callback(
            '/(?:[^%' . self::$charUnreserved . self::$charSubDelims . ']+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $component
        );
    }

    /**
     * @param string $host
     *
     * @return string
     *
     * @throws InvalidArgumentException If the host is invalid.
     */
    private function filterHost(string $host): string
    {
        return strtolower($host);
    }

    /**
     * @param int|null $port
     *
     * @return int|null
     *
     * @throws InvalidArgumentException If the port is invalid.
     */
    private function filterPort($port): ?int
    {
        if ($port === null) {
            return null;
        }

        $port = (int)$port;
        if (1 > $port || 0xffff < $port) {
            throw new InvalidArgumentException(
                sprintf('Invalid port: %d. Must be between 1 and 65535', $port)
            );
        }
        return $port;
    }

    /**
     * @param UriInterface $uri
     * @param array        $keys
     *
     * @return array
     */
    private static function getFilteredQueryString(UriInterface $uri, array $keys): array
    {
        $current = $uri->getQuery();
        if ($current === '') {
            return [];
        }

        $decodedKeys = array_map('rawurldecode', $keys);
        return array_filter(explode('&', $current), function ($part) use ($decodedKeys) {
            return !in_array(rawurldecode(explode('=', $part)[0]), $decodedKeys, true);
        });
    }

    /**
     * @param string      $key
     * @param string|null $value
     *
     * @return string
     */
    private static function generateQueryString($key, $value): string
    {
        // Query string separators ("=", "&") within the key or value need to be encoded
        // (while preventing double-encoding) before setting the query string. All other
        // chars that need percent-encoding will be encoded by withQuery().
        $queryString = strtr($key, self::$replaceQuery);
        if ($value !== null) {
            $queryString .= '=' . strtr($value, self::$replaceQuery);
        }

        return $queryString;
    }

    /**
     * Remove default port
     */
    private function removeDefaultPort(): void
    {
        if ($this->port !== null && $this->isDefaultPort()) {
            $this->port = null;
        }
    }

    /**
     * Whether the URI has the default port of the current scheme.
     * `Psr\Http\Message\UriInterface::getPort` may return null or the standard port. This method can be used
     * independently of the implementation.
     *
     * @return bool
     */
    public function isDefaultPort(): bool
    {
        $defaultPort   = self::DEFAULT_PORTS[$this->getScheme()] ?? null;
        $isDefaultPort = $this->getPort() === $defaultPort;

        return $this->getPort() === null || $isDefaultPort;
    }

    /**
     * Get default port of the current scheme.
     *
     * @return int
     */
    public function getDefaultPort(): int
    {
        return self::DEFAULT_PORTS[$this->getScheme()] ?? 0;
    }

    /**
     * Filters the path of a URI
     *
     * @param string $path
     *
     * @return string
     *
     * @throws InvalidArgumentException If the path is invalid.
     */
    private function filterPath(string $path): string
    {
        if (!$path || $path === '/') {
            return $path;
        }

        if (preg_match('#^[\w/-]+$#', $path) === 1) {
            return $path;
        }

        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $str
     *
     * @return string
     *
     * @throws InvalidArgumentException If the query or fragment is invalid.
     */
    private function filterQueryAndFragment(string $str): string
    {
        if (!$str) {
            return $str;
        }

        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $str
        );
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $match
     *
     * @return string
     */
    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    /**
     * Does this Uri use a standard port?
     *
     * @return bool
     */
    protected function hasStandardPort(): bool
    {
        return ($this->scheme === 'http' && $this->port === 80) || ($this->scheme === 'https' && $this->port === 443);
    }

    /**
     * Validate state
     */
    private function validateState(): void
    {
        if ($this->host === '' && ($this->scheme === 'http' || $this->scheme === 'https')) {
            $this->host = self::HTTP_DEFAULT_HOST;
        }

        if ($this->getAuthority() === '') {
            if (0 === strpos($this->path, '//')) {
                throw new InvalidArgumentException('The path of a URI without an authority must not start with two slashes "//"');
            }

            if ($this->scheme === '' && false !== strpos(explode('/', $this->path, 2)[0], ':')) {
                throw new InvalidArgumentException('A relative URI must not have a path beginning with a segment containing a colon');
            }
        } elseif (isset($this->path[0]) && $this->path[0] !== '/') {
            $this->path = '/' . $this->path;
        }
    }
}
