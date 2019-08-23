<?php
namespace App\WebSocket\Game\Logic;

use App\WebSocket\Game\Core\AStrategy;
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Core\JokerPoker;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;

/**
 *  翻倍处理
 */ 
  
 class GetSingerCard extends AStrategy
 {
	/**
	 * 执行方法
	 */         
	public function exec()
    {
		$card = JokerPoker::getOneCard();
		$data = array('card'=>$card);	
		$data = Packet::packFormat('OK', 0, $data);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::GET_SINGER_CARD_RESP);
		return $data;		    
	}
}