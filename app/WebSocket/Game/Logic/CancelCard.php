<?php
namespace App\WebSocket\Game\Logic;

use App\WebSocket\Game\Core\AStrategy;
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;
/**
 *  取消翻倍
 */ 
  
 class CancelCard extends AStrategy
 {
	/**
	 * 执行方法
	 */         
	public function exec()
    {
		//处理扣金币逻辑，暂时不处理原封不动发回去    
		$data = Packet::packFormat('OK', 0, $this->_params['data']);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::CANCEL_CARD_RESP);
		return $data; 
	}
}