<?php

namespace Swoft\Http\Server;

/**
 * Class Payload
 * - 使用它代替返回原始数据
 * - 可以设置 http status
 * @package Swoft\Http\Server
 */
class Payload
{
    /**
     * @var int The http status for response
     */
    private $status = 200;

    /**
     * @var mixed The body data for response
     */
    public $data;

    /**
     * @param mixed $data
     * @param int $status
     * @return static
     */
    public static function make($data = null, int $status = 0): self
    {
        if ($status) {
            $self = new static($data);

            return $self->setStatus($status);
        }

        return new static($data);
    }

    /**
     * Payload constructor.
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Payload
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
