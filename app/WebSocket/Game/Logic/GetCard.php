<?php
namespace App\WebSocket\Game\Logic;

use App\WebSocket\Game\Core\AStrategy;
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Core\JokerPoker;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;

/**
 *  获取卡牌信息
 */ 
  
 class GetCard extends AStrategy
 {
    /**
     * 执行方法
     */         
    public function exec()
    {
        $data = JokerPoker::getFiveCard();    
        $data = Packet::packFormat('OK', 0, $data);
        $data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::GET_CARD_RESP);
		return $data;  
    }
}