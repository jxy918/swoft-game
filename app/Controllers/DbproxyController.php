<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Controllers;

use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;

use App\Lib\DbproxyInterface;
use App\Lib\NotifyInterface;
use Swoft\Rpc\Client\Bean\Annotation\Reference;

/**
 * Class DbproxyController
 * @Controller(prefix="/dbproxy")
 * @package App\Controllers
 */
class DbproxyController{
    /**
     * @Reference(name="dbproxy", version="1.0.0")
     *
     * @var DbproxyInterface
     */
    private $dbproxyService;


    /**
     * @Reference(name="notify", version="1.0.0")
     *
     * @var NotifyInterface
     */
    private $notifyService;


    /**
     * this is a example action. access uri path: /dbproxy
     * @RequestMapping(route="/dbproxy", method=RequestMethod::GET)
     * @return array
     */
    public function execProc(): array
    {
        $result = $this->dbproxyService->execProc('accounts_mj', 'sp_account_get_by_uid',[10004]);
        $result1 = $this->dbproxyService->execProc('activity_mj', 'sp_winlist_s',[1,10032,'',0]);
        return [$result, $result1];
    }

    /**
     * this is a example action. access uri path: /dbproxy1
     * @RequestMapping(route="/dbproxy1", method=RequestMethod::GET)
     * @return array
     */
    public function deferExecProc(): array
    {
        $result = $this->dbproxyService->deferExecProc('accounts_mj', 'sp_account_get_by_uid',[10004])->getResult();
        $result1 = $this->dbproxyService->deferExecProc('activity_mj', 'sp_winlist_s',[1,10032,'',0])->getResult();
        return [$result, $result1];
    }

    /**
 * this is a example action. access uri path: /test_dbproxy
 * @RequestMapping(route="/test_dbproxy", method=RequestMethod::GET)
 * @return array
 */
    public function testDbproxy() {
        //查询用户信息
        $result = $this->dbproxyService->getLobbyInfoByUid(10004);
        $result1 = $this->dbproxyService->deferGetLobbyInfoByUid(10004)->getResult();

        //查询元宝
        $result2 = $this->dbproxyService->getYuanbao(10004);
        $result3 = $this->dbproxyService->deferGetYuanbao(10004)->getResult();

        //添加元宝
        $result4 = $this->dbproxyService->addYuanbao(10004, 10);
        $result5 = $this->dbproxyService->deferAddYuanbao(10004, 10)->getResult();

        //删除元宝
        $result6 = $this->dbproxyService->delYuanbao(10004, 20);
        $result7 = $this->dbproxyService->deferDelYuanbao(10004, 20)->getResult();

        //修改金币
        $result8 = $this->dbproxyService->modifyGold(10004, 20);
        $result9 = $this->dbproxyService->deferModifyGold(10004, 20)->getResult();

        //添加道具
        $result10 = $this->dbproxyService->addProp(10004, 10001, 20);
        $result11 = $this->dbproxyService->deferAddProp(10004, 10001, 20)->getResult();

        //删除道具
        $result12 = $this->dbproxyService->addProp(10004, 10001, 10);
        $result13 = $this->dbproxyService->deferAddProp(10004, 10001, 10)->getResult();

        return [
            'uinfo'=>$result,
            'deferUinfo'=>$result1,
            'getYuanbao'=>$result2,
            'deferGetYuanbao'=>$result3,
            'addYuanbao'=>$result4,
            'deferAddYuanbao'=>$result5,
            'delYuanbao'=>$result6,
            'deferDelYuanbao'=>$result7,
            'modifyGold'=>$result8,
            'deferModifyGold'=>$result9,
            'addProp'=>$result10,
            'deferAddProp'=>$result11,
            'delProp'=>$result12,
            'deferDelProp'=>$result13
        ];
    }

    /**
     * this is a example action. access uri path: /test_dbproxy
     * @RequestMapping(route="/test_notify", method=RequestMethod::GET)
     * @return array
     */
    public function testNotify() {
        $result = $this->notifyService->notify('aaaaaaaaabbb');
        return $result;
    }
}
