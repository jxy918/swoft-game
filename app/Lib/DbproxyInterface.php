<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Lib;

use Swoft\Core\ResultInterface;

/**
 * The interface of dbproxy service
 *
 * @method ResultInterface deferExecProc(string $dbname, string $sp, array $params)
 * @method ResultInterface deferGetLobbyInfoByUid(int $uid)
 * @method ResultInterface deferGetLobbyInfoByName(string $uname)
 * @method ResultInterface deferGetGameInfoByUid(int $uid)
 * @method ResultInterface deferGetYuanbao(int $uid)
 * @method ResultInterface deferAddYuanbao(int $uid, int $quantity, int $type_id, int $type_id_sub, int $gid , int $kind_id, string $summary)
 * @method ResultInterface deferDelYuanbao(int $uid, int $quantity, int $type_id, int $type_id_sub, string $version, int $gid, int $kind_id)
 * @method ResultInterface deferModifyGold(int $uid, int $quantity, int $type_id, int $type_id_sub, int $game_id, string $version, int $kind_id, string $summary)
 * @method ResultInterface deferAddProp(int $uid, int $prop_id , int $quantity, int $expiry_time = 2145888000, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add')
 * @method ResultInterface deferDelProp(int $uid, int $prop_id , int $quantity, $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add')
 */
interface DbproxyInterface
{
    public function execProc(string $dbname, string $sp, array $params);
    public function getLobbyInfoByUid(int $uid);
//  public function getLobbyInfoByName(string $uname);
//  public function getGameInfoByUid(int $uid);
    public function getYuanbao(int $uid);
    public function addYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
    public function delYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
    public function modifyGold(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
    public function addProp(int $uid, int $prop_id , int $quantity, int $expiry_time = 2145888000, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add');
    public function delProp(int $uid, int $prop_id , int $quantity, $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add');
}