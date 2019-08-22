<?php declare(strict_types=1);


namespace Swoft\Task\Contract;


use Swoole\Server;

interface RequestInterface
{
    /**
     * @return Server
     */
    public function getServer(): Server;

    /**
     * @return int
     */
    public function getTaskId(): int;

    /**
     * @return int
     */
    public function getSrcWorkerId(): int;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getData(): string;

    /**
     * @return string
     */
    public function getName(): string;

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
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getExtKey(string $name, $default = null);
}