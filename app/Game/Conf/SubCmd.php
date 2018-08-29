<?php
namespace App\Game\Conf;

/**
 *子命令字定义，h5客户端也应有一份对应配置，REQ结尾一般是客户端请求过来的子命令字， RESP服务器返回给客户端处理子命令字
 */

class SubCmd
{
    //系统子命令字，对应MainCmd.CMD_SYS
    const LOGIN_FAIL_RESP = 100;			//登陆失败消息下发命
    const HEART_ASK_REQ  = 101;			//心跳请求处理后下发
    const HEART_ASK_RESP = 102;        //心跳请求响应处理（响应需要编写对应的路由处理逻辑）
    const BROADCAST_MSG_REQ = 103;     //系统广播消息请求
    const BROADCAST_MSG_RESP = 104;     //系统广播消息请求

    //游戏逻辑子命令字,对应MainCmd.CMD_GAME
    const GET_CARD_REQ = 201;				//获取卡牌请求，客户端使用
    const GET_CARD_RESP = 202;			//获取卡牌响应，服务端使用
    const SEND_CARD_REQ = 203;			//发送卡牌请求，客户端使用
    const SEND_CARD_RESP = 204;			//发送卡牌响应，服务端使用
    const TURN_CARD_REQ = 205;			//翻开卡牌请求，客户端使用
    const TURN_CARD_RESP = 206;			//翻开卡牌响应，服务端使用
    const CANCEL_CARD_REQ = 207;			//取消卡牌请求，客户端使用
    const CANCEL_CARD_RESP = 208;			//取消卡牌响应，服务端使用
    const IS_DOUBLE_REQ = 209;			//结果是否翻倍请求，客户端使用
    const IS_DOUBLE_RESP = 210;			//结果是否翻倍响应，服务端使用
    const GET_SINGER_CARD_REQ = 211;		//获取翻倍单张卡牌请求，客户端使用
    const GET_SINGER_CARD_RESP = 212;	//获取翻倍单张卡牌响应，服务端使用
    const CHAT_MSG_REQ = 213;			//聊天消息请求，客户端使用
    const CHAT_MSG_RESP = 214;			//聊天消息响应，服务端使用
}