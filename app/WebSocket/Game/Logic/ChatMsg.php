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
//        var_dump(App::getPool('demoRedis'));
//        $result = Db::query('CALL `sp_account_get_by_uid` (?);',array(52),'default.master.accounts_mj')->getResult();
//        var_dump('testing mysql---------------------:',$result);
//        $result1 = Query::table(Account::class)->selectDb('accounts_mj')->where('uid',50)->limit(1)->get()->getResult();
//        var_dump('AR testing mysql---------------------:',$result1);

		//原封不动发回去    
		$data = Packet::packFormat('OK', 0, $this->_params['data']);
		$data = Packet::packEncode($data, MainCmd::CMD_GAME, SubCmd::CHAT_MSG_RESP);
		return $data; 
	}
}
