<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Event\Listeners;

use Swoft\Bean\Annotation\Listener;
use Swoft\Core\RequestContext;
use Swoft\Db\AbstractDbConnection;
use Swoft\Db\Helper\DbHelper;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Helper\PoolHelper;
use Swoft\Log\Log;

/**
 * TransactionRelease
 *
 * @Listener(AppEvent::RESOURCE_RELEASE_BEFORE)
 */
class ResourceReleaseBeforeListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        $contextTsKey  = DbHelper::getContextTransactionsKey();
        $contextCntKey = PoolHelper::getContextCntKey();
        $transactions  = RequestContext::getContextDataByKey($contextTsKey, []);
        $connections   = RequestContext::getContextDataByKey($contextCntKey, []);

        if (empty($connections) || empty($transactions)) {
            return;
        }

        foreach ($transactions as $instance => $tsStack) {
            if (!($tsStack instanceof \SplStack)) {
                continue;
            }
            while (!$tsStack->isEmpty()) {
                $connectId = $tsStack->pop();
                if (!isset($connections[$connectId])) {
                    continue;
                }
                $connection = $connections[$connectId];
                if ($connection instanceof AbstractDbConnection) {
                    $connection->rollback();

                    Log::error(sprintf('%s transaction is not committed or rollbacked', \get_class($connection)));
                }
            }
        }
    }
}
