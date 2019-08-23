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
  
 class IsDouble extends AStrategy
 {
	/**
	 * 执行方法
	 */         
	public function exec()
    {
		$card = isset($this->_params['data']['card']) ? $this->_params['data']['card'] : 2; ;//明牌
		$pos = isset($this->_params['data']['pos']) && ($this->_params['data']['pos'] < 4) ? intval($this->_params['data']['pos']) : 2;//我选中的位置 0123
		$res = JokerPoker::getIsDoubleCard($card, $pos);
		$res['bean'] = 0;
		$res['bet'] = 0;		
		$data = Packet::packFormat('OK', 0, $res);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::IS_DOUBLE_RESP);
		return $data;		    
	}
}
