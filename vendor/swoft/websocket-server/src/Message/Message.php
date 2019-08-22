<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Message;

use JsonSerializable;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Concern\CommonProtocolDataTrait;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class Message
 *
 * @since 2.0
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Message implements JsonSerializable
{
    use CommonProtocolDataTrait;

    /**
     * Message command. it's must exists. eg: 'home.index'
     *
     * @var string
     */
    private $cmd;

    /**
     * @param string $cmd
     * @param mixed  $data
     *
     * @param array  $ext
     *
     * @return Message
     */
    public static function new(string $cmd, $data, array $ext = []): self
    {
        /** @var self $self */
        $self = Swoft::getBean(self::class);

        // Set properties
        $self->cmd  = $cmd;
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
     */
    public function setCmd(string $cmd): void
    {
        $this->cmd = $cmd;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
