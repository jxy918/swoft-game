<?php declare(strict_types=1);

namespace Swoft\Http\Message\Concern;

use Swoft\Http\Message\Cookie;
use function array_replace;
use function is_array;
use function is_object;
use function is_string;

/**
 * Trait CookiesTrait
 *
 * @since 2.0
 */
trait CookiesTrait
{
    /**
     * Default cookie properties
     *
     * @var array
     */
    private static $cookieDefaults = [
        'value'    => '',
        'domain'   => '',
        'path'     => '',
        'expires'  => 0,
        'secure'   => false,
        'httpOnly' => false,
        'hostOnly' => false,
    ];

    /**
     * Cookie
     *
     * @var array[]
     */
    protected $cookies = [];

    /**
     * Set cookie
     *
     * @param string              $name  Cookie name
     * @param string|array|Cookie $value Cookie value, or cookie properties
     *
     * @return $this
     */
    public function setCookie(string $name, $value): self
    {
        if (is_string($value)) {
            $cookieItem = Cookie::DEFAULTS;

            // append value
            $cookieItem['value']  = $value;
            $this->cookies[$name] = $cookieItem;
        } elseif (is_object($value) && $value instanceof Cookie) {
            $this->cookies[$name] = $value->toArray();
        } elseif (is_array($value)) {
            $this->cookies[$name] = array_replace(Cookie::DEFAULTS, $value);
        }

        return $this;
    }

    /**
     * @param string              $name
     * @param string|array|Cookie $value
     *
     * @return CookiesTrait
     */
    public function withCookie(string $name, $value): self
    {
        $new = clone $this;
        $new->setCookie($name, $value);

        return $new;
    }

    /**
     * Remove an cookie by name
     *
     * @param string $name
     *
     * @return self
     */
    public function delCookie(string $name): self
    {
        if (isset($this->cookies[$name])) {
            unset($this->cookies[$name]);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return CookiesTrait
     */
    public function withoutCookie(string $name): self
    {
        $new = clone $this;
        $new->delCookie($name);

        return $new;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Set cookies
     *
     * @param array $cookies Cookies data. If is empty, will clear cookies
     *
     * @return self
     */
    public function setCookies(array $cookies): self
    {
        if (!$cookies) {
            $this->cookies = [];
            return $this;
        }

        foreach ($cookies as $name => $value) {
            $this->setCookie($name, $value);
        }

        return $this;
    }

    /**
     * @param array $cookies
     *
     * @return $this
     */
    public function withCookies(array $cookies): self
    {
        $new = clone $this;
        $new->setCookies($cookies);

        return $new;
    }

    /**
     * @return CookiesTrait
     */
    public function withoutCookies(): self
    {
        $new = clone $this;
        $new->setCookies([]);

        return $new;
    }
}
