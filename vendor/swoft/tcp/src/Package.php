<?php declare(strict_types=1);

namespace Swoft\Tcp;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Server\Concern\CommonProtocolDataTrait;
use Swoft\Stdlib\Helper\JsonHelper;
use function bean;

/**
 * Class Package - Request package structure
 *
 * @since 2.0.3
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Package
{
    use CommonProtocolDataTrait;

    /**
     * Message request command. it's must exists.
     *
     * @var string
     */
    private $cmd = '';

    /**
     * @param string $route
     * @param        $data
     * @param array  $ext
     *
     * @return Package
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(string $route, $data, array $ext = []): self
    {
        /** @var self $self */
        $self = bean(self::class);

        // Set properties
        $self->cmd  = $route;
        $self->data = $data;
        $self->ext  = $ext;

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'cmd'  => $this->cmd,
            'data' => $this->data,
            'ext'  => $this->ext,
        ];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return JsonHelper::encode($this->toArray());
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
    public function getCmd(): string
    {
        return $this->cmd;
    }

    /**
     * @param string $cmd
     *
     * @return Package
     */
    public function setCmd(string $cmd): Package
    {
        $this->cmd = $cmd;
        return $this;
    }
}
