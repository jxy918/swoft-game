<?php
namespace App\Game\Logic;

use App\Game\Core\AStrategy;
use App\Game\Core\Packet;
use App\Game\Core\JokerPoker;
use App\Game\Conf\MainCmd;
use App\Game\Conf\SubCmd;

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