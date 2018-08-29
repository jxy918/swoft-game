<?php
namespace App\Game\Logic;

use App\Game\Core\AStrategy;
use App\Game\Core\Packet;
use App\Game\Core\JokerPoker;
use App\Game\Conf\MainCmd;
use App\Game\Conf\SubCmd;

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