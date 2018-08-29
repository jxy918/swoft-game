<?php
namespace App\Game\Logic;

use App\Game\Core\AStrategy;
use App\Game\Core\Packet;
use App\Game\Core\JokerPoker;
use App\Game\Conf\MainCmd;
use App\Game\Conf\SubCmd;


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
