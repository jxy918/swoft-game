<?php declare(strict_types=1);

namespace Swoft\Http\Message\Concern;

use function array_map;
use function array_merge;
use function bean;
use function implode;
use InvalidArgumentException;
use function is_array;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use ReflectionException;
use function strtolower;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Stream\Stream;
use function trim;

/**
 * Class MessageTrait
 *
 * @since 2.0
 */
trait MessageTrait
{
    /**
     * Map of all registered headers, as original name => array of values
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Map of lowercase header name => original name at registration
     *
     * @var array
     */
    protected $headerNames = [];

    /**
     * string
     *
     * @var string
     */
    protected $protocol = '';

    /**
     * Stream interface
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol ?: '1.1';
    }

    /**
     * @inheritdoc
     *
     * @param string $version HTTP protocol version
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;

        $new->protocol = $version;
        return $new;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name): bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name): array
    {
        $header = strtolower($name);
        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];
        return $this->headers[$header];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     *
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * Get all headerLines
     *
     * @return array
     */
    public function getHeaderLines(): array
    {
        $headers = [];
        foreach ($this->getHeaders() as $name => $header) {
            $headers[$name] = implode(', ', $header);
        }

        return $headers;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value      = $this->trimHeaderValues($value);
        $normalized = strtolower($name);
        $new        = clone $this;

        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }

        $new->headerNames[$normalized] = $name;
        $new->headers[$name]           = $value;

        return $new;
    }

    /**
     * @param string          $name  Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     *
     * @return static
     * @throws InvalidArgumentException for invalid header names or values.
     * @see MessageInterface::withAddedHeader()
     *
     */
    public function withAddedHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value      = $this->trimHeaderValues($value);
        $normalized = strtolower($name);
        $new        = clone $this;

        if (isset($new->headerNames[$normalized])) {
            $name                = $this->headerNames[$normalized];
            $new->headers[$name] = array_merge($this->headers[$name], $value);

            return $new;
        }

        $new->headerNames[$normalized] = $name;
        $new->headers[$name]           = $value;

        return $new;
    }

    public function withHeaders(array $headers): self
    {
        $new = clone $this;
        foreach ($headers as $header => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $value      = $new->trimHeaderValues($value);
            $normalized = strtolower($header);

            if (isset($new->headerNames[$normalized])) {
                $headerName = $new->headerNames[$normalized];
                $oldValues  = $new->headers[$headerName];
                // re-save
                $new->headers[$headerName] = array_merge($oldValues, $value);
                continue;
            }

            $new->headerNames[$normalized] = $header;
            $new->headers[$header]         = $value;
        }

        return $new;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     *
     * @return static
     */
    public function withoutHeader($name)
    {
        $normalized = strtolower($name);
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $name = $this->headerNames[$normalized];
        $new  = clone $this;

        unset($new->headers[$name], $new->headerNames[$normalized]);
        return $new;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function getBody(): StreamInterface
    {
        if (!$this->stream) {
            $this->stream = bean(Stream::class);
        }

        return $this->stream;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     *
     * @return static
     * @throws InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new         = clone $this;
        $new->stream = $body;

        return $new;
    }

    /**
     * Set headers
     *
     * @param array $headers
     *
     * @return $this
     */
    protected function setHeaders(array $headers): self
    {
        $this->headerNames = $this->headers = [];
        foreach ($headers as $header => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }

            $value      = $this->trimHeaderValues($value);
            $normalized = strtolower($header);

            if (isset($this->headerNames[$normalized])) {
                $headerName = $this->headerNames[$normalized];
                $oldValues  = $this->headers[$headerName];
                // re-save
                $this->headers[$headerName] = array_merge($oldValues, $value);
                continue;
            }

            $this->headerNames[$normalized] = $header;
            $this->headers[$header]         = $value;
        }
        return $this;
    }

    /**
     * @param array $headers [name => value string]
     */
    protected function initializeHeaders(array $headers): void
    {
        foreach ($headers as $name => $value) {
            $name = strtolower($name);

            $this->headers[$name]     = [$value];
            $this->headerNames[$name] = $name;
        }
    }

    /**
     * Trims whitespace from the header values.
     *
     * Spaces and tabs ought to be excluded by parsers when extracting the field value from a header field.
     *
     * header-field = field-name ":" OWS field-value OWS
     * OWS          = *( SP / HTAB )
     *
     * @param string[] $values Header values
     *
     * @return string[] Trimmed header values
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
     */
    private function trimHeaderValues(array $values): array
    {
        return array_map(function ($value) {
            return trim((string)$value, " \t");
        }, $values);
    }
}
