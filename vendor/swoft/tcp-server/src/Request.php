<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Tcp\Package;
use Swoft\Tcp\Server\Contract\RequestInterface;

/**
 * Class Request
 *
 * @since 2.0
 * @Bean(name="tcpRequest", scope=Bean::PROTOTYPE)
 */
class Request implements RequestInterface
{
    /**
     * Receiver fd
     *
     * @var int
     */
    private $fd = -1;

    /**
     * @var string
     */
    private $rawData = '';

    /**
     * @var int
     */
    private $reactorId = -1;

    /**
     * @var Package
     */
    private $package;

    /**
     * @param int    $fd
     * @param string $data
     * @param int    $reactorId
     *
     * @return self
     */
    public static function new(int $fd, string $data, int $reactorId): self
    {
        /** @var self $self */
        $self = bean('tcpRequest');

        // Set properties
        $self->fd      = $fd;
        $self->rawData = $data;
        $self->reactorId = $reactorId;

        return $self;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @param int $fd
     */
    public function setFd(int $fd): void
    {
        $this->fd = $fd;
    }

    /**
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }

    /**
     * @return Package
     */
    public function getPackage(): Package
    {
        return $this->package;
    }

    /**
     * @param Package $package
     */
    public function setPackage(Package $package): void
    {
        $this->package = $package;
    }
}
