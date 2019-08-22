<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Contract;

use Swoole\Server;

/**
 * Class RequestInterface
 *
 * @since 2.0
 */
interface RequestInterface
{
    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return string
     */
    public function getInterface(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return array
     */
    public function getParams(): array;

    /**
     * @return array
     */
    public function getExt(): array;

    /**
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getExtKey(string $key, $default = null);

    /**
     * @param int        $index
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getParam(int $index, $default = null);

    /**
     * @return string
     */
    public function getData(): string;

    /**
     * @return Server
     */
    public function getServer(): Server;

    /**
     * @return int
     */
    public function getFd(): int;

    /**
     * @return int
     */
    public function getReactorId(): int;

    /**
     * @return float
     */
    public function getRequestTime(): float;

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute(string $key, $value): void;

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null);
}