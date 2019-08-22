<?php declare(strict_types=1);

namespace Swoft\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Swoft\Http\Message\Concern\MessageTrait;
use function explode;
use function in_array;
use function preg_match;
use function str_replace;
use function strpos;
use function strtoupper;

/**
 * Class Request
 *
 * @since 2.0
 */
class PsrRequest implements RequestInterface
{
    // Message trait
    use MessageTrait;

    /**
     * Method name
     *
     * @var string
     */
    protected $method;

    /**
     * Request target
     *
     * @var string
     */
    protected $requestTarget;

    /**
     * Uri interface
     *
     * @var UriInterface
     */
    protected $uri;

    /**
     * Retrieves the message's request target.
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }
        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    /**
     * Return an instance with the specific request-target.
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     *
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }

        $new = clone $this;

        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     *
     * @param string $method Case-sensitive method.
     *
     * @return static
     * @throws InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $method  = strtoupper($method);
        $methods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'HEAD'];

        if (!in_array($method, $methods, true)) {
            throw new InvalidArgumentException('Invalid Method');
        }
        $new         = clone $this;
        $new->method = $method;

        return $new;
    }

    /**
     * Retrieves the URI instance.
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          New request URI to use.
     * @param bool         $preserveHost Preserve the original state of the Host header.
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $new      = clone $this;
        $new->uri = $uri;

        if (!$preserveHost) {
            $new->updateHostByUri();
        }

        return $new;
    }

    /**
     * Is GET method
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Is POST method
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Is PATCH method
     *
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method === 'PATCH';
    }

    /**
     * Is PUT method
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Is DELETE method
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    /**
     * Update Host Header according to Uri
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.4
     */
    protected function updateHostByUri(): void
    {
        $host = $this->uri->getHost();
        if ($host === '') {
            return;
        }

        if (($port = $this->uri->getPort()) !== null) {
            $host .= ':' . $port;
        }

        if ($this->hasHeader('host')) {
            $header = $this->getHeaderLine('host');
        } else {
            $header = 'host';
            // save name
            $this->headerNames['host'] = 'Host';
        }

        // Ensure Host is the first header.
        $this->headers = [$header => [$host]] + $this->headers;
    }

    /**
     * Get client supported languages from header
     * eg: `Accept-Language:zh-CN, zh;q=0.8, en;q=0.5`
     *
     * @return array [['zh-CN', 1], ['zh', 0.8]]
     */
    public function getAcceptLanguages(): array
    {
        $ls = [];

        if ($value = $this->getHeaderLine('Accept-Language')) {
            $value = str_replace(' ', '', $value);

            if (strpos($value, ',')) {
                $nodes = explode(',', $value);
            } else {
                $nodes = [$value];
            }

            foreach ($nodes as $node) {
                if (strpos($node, ';')) {
                    $info    = explode(';', $node);
                    $info[1] = (float)substr($info[1], 2);
                } else {
                    $info = [$node, 1.0];
                }

                $ls[] = $info;
            }
        }

        return $ls;
    }

    /**
     * get client supported languages from header
     * eg: `Accept-Encoding:gzip, deflate, sdch, br`
     *
     * @return array
     */
    public function getAcceptEncodes(): array
    {
        $ens = [];

        if ($value = $this->getHeaderLine('Accept-Encoding')) {
            if (strpos($value, ';')) {
                [$value,] = explode(';', $value, 2);
            }

            $value = str_replace(' ', '', $value);
            $ens   = explode(',', $value);
        }

        return $ens;
    }
}
