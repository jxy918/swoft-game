<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/8/21
 * Time: 17:32
 */






const RPC_EOL = "\r\n\r\n";

function request($host, $class, $method, $param, $version = '1.0', $ext = []) {
    $fp = stream_socket_client($host, $errno, $errstr);
    if (!$fp) {
        throw new Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
    }

    $req = [
        "jsonrpc" => '2.0',
        "method" => sprintf("%s::%s::%s", $version, $class, $method),
        'params' => $param,
        'id' => '',
        'ext' => $ext,
    ];
    $data = json_encode($req) . RPC_EOL;
    fwrite($fp, $data);

    $result = '';
    while (!feof($fp)) {
        $tmp = stream_socket_recvfrom($fp, 1024);

        if ($pos = strpos($tmp, RPC_EOL)) {
            $result .= substr($tmp, 0, $pos);
            break;
        } else {
            $result .= $tmp;
        }
    }

    fclose($fp);
    return json_decode($result, true);
}

$ret = request('tcp://127.0.0.1:18307', \App\Rpc\Lib\UserInterface::class, 'getList',  [1, 2], "1.0");
var_dump($ret);

$uid = 10001;
$url = 'tcp://127.0.0.1:18307';
$result = request($url, \App\Rpc\Lib\DbproxyInterface::class, 'execProc',  ['accounts_mj', 'sp_account_get_by_uid', [$uid]], "1.0");
var_dump($result);


//$result = call('App\Lib\DbproxyInterface', '1.0.0', 'execProc', ['accounts_mj', 'sp_account_get_by_uid', [$uid]]);
//$result1 = call('App\Lib\DemoInterface', '1.0.1', 'getUsers', [[$uid]]);
//$result2 = call('App\Lib\DbproxyInterface', '1.0.0', 'execProc', ['activity_mj', 'sp_winlist_s', [1,10032,'',0]]);
//
//
//$result3 = call('App\Lib\DbproxyInterface', '1.0.0', 'getYuanbao', [$uid]);








