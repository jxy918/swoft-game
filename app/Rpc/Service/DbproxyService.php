<?php declare(strict_types=1);


namespace App\Rpc\Service;

use App\Rpc\Lib\DbproxyInterface;
use Swoft\Rpc\Server\Annotation\Mapping\Service;
use Swoft\Db\DB;

/**
 * Class DbproxyService
 *
 * @since 2.0
 *
 * @Service()
 */
class DbproxyService implements DbproxyInterface
{
    /**
     * 执行存储过程获取返回值
     * @param string $dbname
     * @param string $sp
     * @param array $params
     * @return array
     */
    public function execProc(string $dbname, string $sp, array $params):array
    {
        //实现操作db
        $dbname = "{$dbname}";
        $spname  = "CALL `{$dbname}`.`{$sp}` (";
        $cnt = count($params);
        $p = array();
        for($i = 0 ; $i < $cnt; $i++) {
            $p[] = $params[$i];
        }
        if(!empty($p)) {
            $spname .= implode(',', $p);
        }
        $spname .= ")";
        $result = DB::select($spname);
        return $result;
    }

//    /**
//     * 根据uid获取大厅的用户信息
//     * @param int $uid
//     */
//    public function getLobbyInfoByUid(int $uid)
//    {
//        return $this->execProc('account_mj','sp_account_get_by_uid',array($uid));
//    }
//
//    /**
//     * 根据uname获取用户信息
//     * @param string $uname
//     * @return array
//     * @throws \Swoft\Db\Exception\DbException
//     */
//    public function getLobbyInfoByName(string $uname)
//    {
//        return $this->execProc('account_mj','sp_account_get_by_username',array($uname));
//    }
//
//    /**
//     * @param int $uid
//     */
//    public function getGameInfoByUid(int $uid)
//    {
//        return $this->execProc('game_mj','sp_user_get_by_uid',array($uid));
//    }
//
//    /**
//     * 根据uid获取用户信息
//     * @param int $uid
//     * @return array
//     * @throws \Swoft\Db\Exception\DbException
//     */
//    public function getYuanbao(int $uid)
//    {
//        return $this->execProc('account_mj','sp_yuanbao_get',array($uid));
//    }
//
//    /**
//     * 添加元宝
//     * @param int $uid
//     * @param int $quantity
//     * @param int $type_id
//     * @param int $type_id_sub
//     * @param int $gid
//     * @param int $kind_id
//     * @param string $summary
//     * @return array
//     * @throws \Swoft\Db\Exception\DbException
//     */
//    public function addYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0 , int $gid = 0, int $kind_id = 0, string $summary = 'web exec')
//    {
//        $params = array($uid, $quantity);
//        $result = $this->execProc('account_mj','sp_yuanbao_add',$params);
//        if(isset($result[0]['quantity']) && isset($result[0]['quantity1'])) {
//            //写入流水
//            $params2 = array(
//                time(),
//                $uid,
//                $gid,
//                $type_id,
//                $type_id_sub,
//                $result[0]['quantity1'],
//                $result[0]['quantity'],
//                $quantity,
//                0,
//                0,
//                $kind_id,
//                $summary
//            );
//            $res = $this->execProc('log_comm_mj', 'sp_yuanbao_i', $params2);
//            if(!$res) {
//                App::error('addYuanbao_sp_yuanbao_i:' . $res);
//            }
//        }
//        return $result;
//    }
//
//    /**
//     * 删除元宝
//     * @param int $uid
//     * @param int $quantity
//     * @param int $type_id
//     * @param int $type_id_sub
//     * @param int $gid
//     * @param int $kind_id
//     * @param string $summary
//     * @return array
//     * @throws \Swoft\Db\Exception\DbException
//     */
//    public function delYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec')
//    {
//        $params = array($uid, $quantity);
//        $result = $this->execProc('account_mj','sp_yuanbao_del2',$params);
//        if(isset($result[0]['quantity']) && isset($result[0]['quantity1'])) {
//            //写入流水
//            $params2 = array(
//                time(),
//                $uid,
//                $gid,
//                $type_id,
//                $type_id_sub,
//                $result[0]['quantity1'],
//                $result[0]['quantity'],
//                $quantity,
//                0,
//                0,
//                $kind_id,
//                $summary
//            );
//            $res = $this->execProc('log_comm_mj', 'sp_yuanbao_i', $params2);
//            if(!$res) {
//                App::error('delYuanbao_sp_yuanbao_i:' . $res);
//            }
//        }
//        return $result;
//    }
//
//    /**
//     * 修改用户金币
//     * @param int $uid
//     * @param int $quantity
//     * @param int $type_id
//     * @param int $type_id_sub
//     * @param int $gid
//     * @param int $kind_id
//     * @param string $summary
//     * @return array
//     * @throws \Swoft\Db\Exception\DbException
//     */
//    public function modifyGold(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec')
//    {
//        $params = array($uid, $quantity);
//        $result = $this->execProc('account_mj','sp_account_gold_upd2',$params);
//        if(isset($result[0]['quantity']) && isset($result[0]['quantity1'])) {
//            //写入流水
//            $params2 = array(
//                time(),
//                $uid,
//                $gid,
//                $type_id,
//                $type_id_sub,
//                $result[0]['quantity1'],
//                $result[0]['quantity'],
//                $quantity,
//                0,
//                0,
//                $kind_id,
//                $summary
//            );
//            $res = $this->execProc('log_comm_mj', 'sp_gold_i', $params2);
//            if(!$res) {
//                App::error('modifyGold_sp_gold_i:' . $res);
//            }
//        }
//        return $result;
//    }
//
//    public function addProp(int $uid, int $prop_id , int $quantity, int $expiry_time = 2145888000, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add')
//    {
//        $params = array($uid, $prop_id, $expiry_time, $quantity);
//        $result = $this->execProc('account_mj','sp_account_bag_add',$params);
//        if(isset($result[0]['quantity1']) && isset($result[0]['quantity2'])) {
//            //写入流水
//            $params2 = array(
//                $uid,
//                $type_id,
//                $type_id_sub,
//                $kind_id,
//                $gid,
//                $prop_id,
//                $quantity,
//                $result[0]['quantity1'],
//                $result[0]['quantity2'],
//                time(),
//                $cid,
//                $version,
//                0,
//                $summary
//            );
//            $res = $this->execProc('log_comm_mj', 'sp_bag_i', $params2);
//            if(!$res) {
//                App::error('addProp_sp_bag_i:' . $res);
//            }
//        }
//        return $result;
//    }
//
//    public function delProp(int $uid, int $prop_id , int $quantity, $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add')
//    {
//        $params = array($uid, $prop_id, $quantity);
//        $result = $this->execProc('account_mj','sp_account_bag_del2',$params);
//        var_dump($result);
//        if(isset($result[0]['quantity1']) && isset($result[0]['quantity2'])) {
//            //写入流水
//            $params2 = array(
//                $uid,
//                $type_id,
//                $type_id_sub,
//                $kind_id,
//                $gid,
//                $prop_id,
//                $quantity,
//                $result[0]['quantity1'],
//                $result[0]['quantity2'],
//                time(),
//                $cid,
//                $version,
//                0,
//                $summary
//            );
//            $res = $this->execProc('log_comm_mj', 'sp_bag_i', $params2);
//            if(!$res) {
//                App::error('addProp_sp_bag_i:' . $res);
//            }
//        }
//        return $result;
//
//    }
}