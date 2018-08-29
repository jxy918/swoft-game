<?php
namespace Swoole\Coroutine\MySQL;

/**
 * @since 2.1.3
 */
class Statement
{

    public $affected_rows;
    public $insert_id;
    public $error;
    public $errno;

    /**
     * @param $params[required]
     * @param $timeout[optional]
     * @return mixed
     */
    public function execute($params, $timeout=null){}

    /**
     * @return mixed
     */
    public function __destruct(){}

    /**
     * @return mixed
     */
    public function __sleep(){}

    /**
     * @return mixed
     */
    public function __wakeup(){}


}
