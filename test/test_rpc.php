<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/8/21
 * Time: 17:32
 */

$uid = 10001;
$result = call('App\Lib\DbproxyInterface', '1.0.0', 'execProc', ['accounts_mj', 'sp_account_get_by_uid', [$uid]]);
$result1 = call('App\Lib\DemoInterface', '1.0.1', 'getUsers', [[$uid]]);
$result2 = call('App\Lib\DbproxyInterface', '1.0.0', 'execProc', ['activity_mj', 'sp_winlist_s', [1,10032,'',0]]);


$result3 = call('App\Lib\DbproxyInterface', '1.0.0', 'getYuanbao', [$uid]);


var_dump($result, $result1, $result2, $result3);

function call(string $interface, string $version, string $method, array $params = [])
{
    $fp = stream_socket_client('tcp://127.0.0.1:8099', $errno, $errstr);
    if (!$fp) {
        throw new Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
    }

    $data = [
        'interface' => $interface,
        'version'   => $version,
        'method'    => $method,
        'params'    => $params,
        'logid'     => uniqid(),
        'spanid'    => 0,
    ];

    $data = json_encode($data, JSON_UNESCAPED_UNICODE)."\r\n";
    fwrite($fp, $data);
    $result = fread($fp, 1024);
    fclose($fp);
    return $result;
}
