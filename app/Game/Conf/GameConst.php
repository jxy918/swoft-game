<?php
namespace App\Game\Conf;

/**
 * 游戏常量
 * Class GameConst
 * @package App\Game\Conf
 */
class GameConst
{
    const GM_PROTOCOL_TCP = 1;      //TCP协议
    const GM_PROTOCOL_WEBSOCK = 2;  //WEBSCOKET协议
    const GM_PROTOCOL_HTTP = 3;     //HTTP协议

    const GM_LOG_LEVEL_INFO = 1;    //打印日志错误登录信息
    const GM_LOG_LEVEL_DEBUG = 2;
    const GM_LOG_LEVEL_ERROR = 3;
}