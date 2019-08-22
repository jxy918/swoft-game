<?php declare(strict_types=1);

namespace Swoft\Http\Message;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Stdlib\Helper\ObjectHelper;
use function bean;
use function gmdate;
use function urlencode;

/**
 * Class Cookie
 *
 * @since 2.0
 */
class Cookie
{
    /**
     * Default cookie properties
     */
    public const DEFAULTS = [
        'value'    => '',
        'domain'   => '',
        'path'     => '',
        'expires'  => 0,
        'secure'   => false,
        'httpOnly' => false,
        'hostOnly' => false,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value = '';

    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $domain = '';

    /**
     * @var int
     */
    private $expires = 0;

    /**
     * @var bool
     */
    private $secure  = false;

    /**
     * @var bool
     */
    private $hostOnly = false;

    /**
     * @var bool
     */
    private $httpOnly = false;

    /**
     * @param array $config
     *
     * @return self
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(array $config = []): self
    {
        $self = bean(static::class);

        if ($config) {
            ObjectHelper::init($self, $config);
        }

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'value'    => $this->value,
            'domain'   => $this->domain,
            'path'     => $this->path,
            'expires'  => $this->expires,
            'secure'   => $this->secure,
            'httpOnly' => $this->httpOnly,
            'hostOnly' => $this->hostOnly,
        ];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $result = urlencode($this->name) . '=' . urlencode($this->value);

        if ($this->domain) {
            $result .= '; domain=' . $this->domain;
        }

        if ($this->path) {
            $result .= '; path=' . $this->path;
        }

        if ($timestamp = $this->expires) {
            $result .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
        }

        if ($this->secure) {
            $result .= '; secure';
        }

        // if ($hostOnly) {
        //     $result .= '; HostOnly';
        // }

        if ($this->httpOnly) {
            $result .= '; HttpOnly';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Cookie
     */
    public function setName(string $name): Cookie
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Cookie
     */
    public function setValue(string $value): Cookie
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Cookie
     */
    public function setPath(string $path): Cookie
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Cookie
     */
    public function setDomain(string $domain): Cookie
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     *
     * @return Cookie
     */
    public function setExpires(int $expires): Cookie
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     *
     * @return Cookie
     */
    public function setSecure(bool $secure): Cookie
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHostOnly(): bool
    {
        return $this->hostOnly;
    }

    /**
     * @param bool $hostOnly
     *
     * @return Cookie
     */
    public function setHostOnly(bool $hostOnly): Cookie
    {
        $this->hostOnly = $hostOnly;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     *
     * @return Cookie
     */
    public function setHttpOnly(bool $httpOnly): Cookie
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }
}
