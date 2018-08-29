/** 主命令字定义 **/
var MainCmd = {
    CMD_SYS               :   1, /** 系统类（主命令字）- 客户端使用 **/
    CMD_GAME              :   2, /** 游戏类（主命令字）- 客户端使用 **/
}

/** 子命令字定义 **/
var SubCmd = {
    //系统子命令字，对应MainCmd.CMD_SYS
    LOGIN_FAIL_RESP :  100,
    HEART_ASK_REQ :  101,
    HEART_ASK_RESP : 102,
    BROADCAST_MSG_REQ :  103,
    BROADCAST_MSG_RESP :  104,

    //游戏逻辑子命令字,对应MainCmd.CMD_GAME
    GET_CARD_REQ :  201,
    GET_CARD_RESP :  202,
    SEND_CARD_REQ :  203,
    SEND_CARD_RESP : 204,
    TURN_CARD_REQ  :  205,
    TURN_CARD_RESP  :  206,
    CANCEL_CARD_REQ  :  207,
    CANCEL_CARD_RESP :  208,
    IS_DOUBLE_REQ :  209,
    IS_DOUBLE_RESP : 210,
    GET_SINGER_CARD_REQ  : 211,
    GET_SINGER_CARD_RESP : 212,
    CHAT_MSG_REQ : 213,
    CHAT_MSG_RESP : 214,
}


/** 
 * 路由规则，key主要命令字=》array(子命令字对应策略类名)
 * 每条客户端对应的请求，路由到对应的逻辑处理类上处理 
 *
 */
 var Route = {
    1 : {
        100 : 'loginFail',   //登陆失败
        102 : 'heartAsk',   //心跳处理
        104 : 'broadcast',   //广播消息
    },
    2 : {
        202 : 'getCard',    //获取卡牌
        204 : 'sendCard',
        206 : 'turnCard',
        208 : 'cancelCard',
        210 : 'isDouble',
        212 : 'getSingerCard',
        214 : 'chatMsg',
    },
}

