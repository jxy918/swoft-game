<?php declare(strict_types=1);

namespace App\Process;

use Swoft\Log\Helper\CLog;
use Swoft\Process\Annotation\Mapping\Process;
use Swoft\Process\Contract\ProcessInterface;
use Swoole\Coroutine;
use Swoole\Process\Pool;

use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class Worker1Process
 *
 * @since 2.0
 *
 * @Process(workerId={0,1,2})
 */
class Worker1Process implements ProcessInterface
{
    /**
     * @param Pool $pool
     * @param int $workerId
     * @throws \ErrorException
     */
    public function run(Pool $pool, int $workerId): void
    {
        $conf = [
            'host' => '192.168.7.197',
            'port' => 5672,
            'user' => 'mykj',
            'pwd' => '123456',
            'vhost' => '/',
        ];
        $e_name = 'e_test'; //交换机名
        $q_name = 'q_test'; //队列名
        $k_route = 'key_test'; //路由key

        $connection = new AMQPStreamConnection($conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']);
        $channel = $connection->channel();
        $channel->exchange_declare($e_name, 'topic', false, true, false); //声明初始化交换机
        $channel->queue_declare($q_name, false, true, false, false); //声明初始化一条队列
        $channel->queue_bind($q_name,$e_name , $k_route); //将队列与某个交换机进行绑定，并使用路由关键字
        CLog::info(' [*] Waiting for messages. To exit press CTRL+C');
        $callback = function($msg) use ($workerId) {
            $msg = 'worderid:'.$workerId.$msg->body;
            CLog::info($msg);
        };
        $channel->basic_consume($q_name, '', false, true, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
