<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/18
 * Time: 下午1:06
 */

namespace Swoft\WebSocket\Server\Bean\Annotation;

/**
 * Class WebSocket
 * @package Swoft\WebSocket\Server\Bean\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
class WebSocket
{
    /**
     * @var string
     */
    private $path = '/';

    /**
     * class constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->path = $values['value'];
        }

        if (isset($values['path'])) {
            $this->path = $values['path'];
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
