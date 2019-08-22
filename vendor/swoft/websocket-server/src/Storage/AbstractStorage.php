<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Storage;

/**
 * Class AbstractStorage
 *
 * @package Swoft\WebSocket\Server\Storage
 */
abstract class AbstractStorage
{
    /**
     * server ID
     *
     * @var string
     */
    protected $id;

    public $namespace = '/';

    /**
     * @var array
     */
    private $namespaces = [
        '/' => 'rooms'
    ];


    /*****************************************************************************
     * some methods for rooms(of an namespace)
     ****************************************************************************/

    /**
     * rooms cache
     *
     * @var array
     */
    private $rooms = [];

    /**
     * @param string $name
     */
    public function newRoom(string $name): void
    {

    }

    /**
     * @param int    $fd
     * @param string $name
     */
    public function joinRoom(int $fd, string $name): void
    {

    }

    /**
     * @param int    $fd
     * @param string $name
     */
    public function leaveRoom(int $fd, string $name): void
    {

    }


}
