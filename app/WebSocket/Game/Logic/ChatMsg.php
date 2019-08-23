<?php
namespace App\WebSocket\Game\Logic;

use App\WebSocket\Game\Core\AStrategy;
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;

use Swoft\App;
use Swoft\Db\Query;
use Swoft\Db\Db;
use App\Models\Entity\Account;

 class ChatMsg extends AStrategy
 {
	/**
	 * 执行方法
	 */         
	public function exec() {
		//原封不动发回去    
		$data = Packet::packFormat('OK', 0, $this->_params['data']);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::CHAT_MSG_RESP);
		return $data; 
	}
}
