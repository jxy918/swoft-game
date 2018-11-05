<?php
namespace App\Game\Conf;


/**
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理
 * Class Route
 * @package App\Game\Conf
 */
class Route
{
    /**
     * websocket路由配置，websocke配置和tcp配置需要先去配置（MainCmd)主命令子和(SubCmdSys)子主命令字配置文件
     * @var array
     */
    public static $cmd_map = array(
        //系统请求
        MainCmd::CMD_SYS => array(
            SubCmd::HEART_ASK_REQ =>'HeartAsk',
        ),
        //游戏请求
        MainCmd::CMD_GAME => array(
            SubCmd::GET_CARD_REQ =>'GetCard',
            SubCmd::SEND_CARD_REQ =>'SendCard',
            SubCmd::TURN_CARD_REQ =>'TurnCard',
            SubCmd::CANCEL_CARD_REQ =>'CancelCard',
            SubCmd::IS_DOUBLE_REQ =>'IsDouble',
            SubCmd::GET_SINGER_CARD_REQ =>'GetSingerCard',
            SubCmd::CHAT_MSG_REQ =>'ChatMsg',
        ),
    );
}
