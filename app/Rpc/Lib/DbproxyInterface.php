<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class DbproxyInterface
 *
 * @since 2.0
 */
interface DbproxyInterface
{
    /**
     * @param string $dbname
     * @param string $sp
     * @param array $params
     * @return array
     */
    public function execProc(string $dbname, string $sp, array $params):array;

    /**
     * @param int $uid
     * @return array
     */
//  public function getLobbyInfoByUid(int $uid):array;
//  public function getLobbyInfoByName(string $uname);
//  public function getGameInfoByUid(int $uid);

    /**
     * @param int $uid
     * @return array
     */
//    public function getYuanbao(int $uid):array;
//    public function addYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
//    public function delYuanbao(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
//    public function modifyGold(int $uid, int $quantity, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, int $kind_id = 0, string $summary = 'web exec');
//    public function addProp(int $uid, int $prop_id , int $quantity, int $expiry_time = 2145888000, int $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add');
//    public function delProp(int $uid, int $prop_id , int $quantity, $type_id = 10002, int $type_id_sub = 0, int $gid = 0, $cid = 0, $kind_id = 0, $version = '', $summary = 'web add');
}