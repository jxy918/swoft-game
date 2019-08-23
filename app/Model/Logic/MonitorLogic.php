<?php declare(strict_types=1);


namespace App\Model\Logic;

use App\Model\Entity\User;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\Exception\DbException;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Swoft\Redis\Redis;
use Swoole\Coroutine;

use Swoft\Rpc\Client\Annotation\Mapping\Reference;
use App\Rpc\Lib\DbproxyInterface;

/**
 * Class MonitorProcessLogic
 *
 * @since 2.0
 *
 * @Bean()
 */
class MonitorLogic
{
    /**
     * @Reference(pool="user.pool")
     *
     * @var DbproxyInterface
     */
    private $dbproxyService;

    /**
     * @param Process $process
     *
     * @throws DbException
     */
    public function monitor(Process $process): void
    {
        $process->name('swoft-monitor');

        while (true) {
            $connections = context()->getServer()->getSwooleServer()->connections;
            CLog::info('monitor = ' . json_encode($connections));

            // Database
//            $user = User::find(1)->toArray();
//            CLog::info('user='.json_encode($user));

            $result = $this->dbproxyService->execProc('accounts_mj', 'sp_account_get_by_uid',[10004]);
            CLog::info('user='.json_encode($result));

            // Redis
            Redis::set('test', 'ok');
            CLog::info('test='.Redis::get('test'));

            Coroutine::sleep(3);
        }
    }
}