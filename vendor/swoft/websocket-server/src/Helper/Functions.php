<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/18
 * Time: 下午11:57
 */


if (!function_exists('ws')) {
    /**
     * @return \Swoft\WebSocket\Server\WebSocketServer
     */
    function ws(): \Swoft\WebSocket\Server\WebSocketServer
    {
        return \Swoft\App::$server;
    }
}
