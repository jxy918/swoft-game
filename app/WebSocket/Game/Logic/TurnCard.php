<?php
namespace App\WebSocket\Game\Logic;

use App\WebSocket\Game\Core\AStrategy;
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Core\JokerPoker;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;

/**
 *  翻牌处理
 */ 
  
 class TurnCard extends AStrategy
 {
	/**
	* 执行方法
	*/         
	public function exec()
    {
		$card = isset($this->_params['data']['card']) ? $this->_params['data']['card'] : array(); 
		$card = JokerPoker::getFiveCard($card);
		$res = JokerPoker::getCardType($card);		
		$data = Packet::packFormat('OK', 0, $res);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::TURN_CARD_RESP);
		return $data;		    
	}
}