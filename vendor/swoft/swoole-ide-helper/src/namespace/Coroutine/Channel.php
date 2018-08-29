<?php
namespace Swoole\Coroutine;

/**
 * @since 2.1.3
 */
class Channel
{

    public $capacity;

    /**
     * @param $size[required]
     * @return mixed
     */
    public function __construct($size){}

    /**
     * @return mixed
     */
    public function __destruct(){}

    /**
     * @param $data[required]
     * @return mixed
     */
    public function push($data){}

    /**
     * @return mixed
     */
    public function pop(){}

    /**
     * @return mixed
     */
    public function isEmpty(){}

    /**
     * @return mixed
     */
    public function isFull(){}

    /**
     * @return mixed
     */
    public function close(){}

    /**
     * @return mixed
     */
    public function stats(){}

    /**
     * @return mixed
     */
    public function length(){}

    /**
     * @param $read_list[required]
     * @param $write_list[required]
     * @param $timeout[required]
     * @return mixed
     */
    public static function select($read_list, $write_list, $timeout){}


}
