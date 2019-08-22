<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Testing;

use Swoole\Http\Response;

/**
 * Class MockHttpResponse
 *
 * @since 2.0
 */
class MockHttpResponse extends Response
{
    /**
     * Status success
     */
    public const STATUS200 = 200;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var int
     */
    private $status = 0;

    /**
     * @var string
     */
    private $downFile = '';

    /**
     * @return self
     */
    public static function new(): self
    {
        return new self;
    }

    /**
     * @param mixed $content
     *
     * @return void
     */
    public function end($content = null): void
    {
        $this->content = $content;
    }

    /**
     * @param $html
     */
    public function write($html)
    {
    }

    /**
     * @param      $key
     * @param      $value
     * @param null $ucwords
     */
    public function header($key, $value, $ucwords = null)
    {
        $this->header[$key] = $value;
    }

    /**
     * @param string      $name
     * @param string      $value
     * @param int|string  $expires
     * @param string|null $path
     * @param string      $domain
     * @param bool        $secure
     * @param bool        $httpOnly
     */
    public function cookie(
        $name,
        $value = null,
        $expires = null,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = null
    ) {
        $result = \urlencode($name) . '=' . \urlencode($value);

        if ($domain) {
            $result .= '; domain=' . $domain;
        }

        if ($path) {
            $result .= '; path=' . $path;
        }

        if ($expires) {
            if (\is_string($expires)) {
                $timestamp = \strtotime($expires);
            } else {
                $timestamp = (int)$expires;
            }

            if ($timestamp !== 0) {
                $result .= '; expires=' . \gmdate('D, d-M-Y H:i:s e', $timestamp);
            }
        }

        if ($secure) {
            $result .= '; secure';
        }

        // if ($hostOnly) {
        //     $result .= '; HostOnly';
        // }

        if ($httpOnly) {
            $result .= '; HttpOnly';
        }

        $this->cookie[$name] = $result;
    }

    /**
     * 设置HttpCode，如404, 501, 200
     *
     * @param int    $code
     * @param string $reason
     */
    public function status($code, $reason = null)
    {
        $this->status = $code;
    }

    /**
     * 设置Http压缩格式
     *
     * @param int $level
     */
    public function gzip($level = 1)
    {
    }

    /**
     * 发送静态文件
     *
     * @param      $filename
     * @param null $offset
     * @param null $length
     *
     * @internal param string $level
     */
    public function sendfile($filename, $offset = null, $length = null)
    {
        $this->downFile = $filename;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array|mixed
     */
    public function getHeaders()
    {
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @return mixed
     */
    public function getTrailer()
    {
        return $this->trailer;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function getHeaderKey(string $key, $default = null)
    {
        return $this->header[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function getDownFile(): string
    {
        return $this->downFile;
    }
}
