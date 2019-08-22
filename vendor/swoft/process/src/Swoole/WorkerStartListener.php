<?php declare(strict_types=1);


namespace Swoft\Process\Swoole;


use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Process\Contract\WorkerStartInterface;
use Swoft\Process\ProcessDispatcher;
use Swoft\Process\ProcessEvent;
use Swoft\Process\ProcessPool;
use Swoole\Coroutine;
use Swoole\Process\Pool;
use Swoft\Bean\Annotation\Mapping\Inject;


/**
 * Class WorkerStartListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class WorkerStartListener implements WorkerStartInterface
{
    /**
     * @Inject()
     *
     * @var ProcessDispatcher
     */
    private $processDispatcher;

    /**
     * @param Pool $pool
     * @param int  $workerId
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function onWorkerStart(Pool $pool, int $workerId): void
    {
        // Init
        ProcessPool::$processPool->initProcessPool($pool);

        // Before
        Swoft::trigger(ProcessEvent::BEFORE_PROCESS, $this, $pool, $workerId);

        // Dispatcher
        $this->processDispatcher->dispatcher($pool, $workerId);

        // After
        Swoft::trigger(ProcessEvent::BEFORE_PROCESS, $this, $pool, $workerId);
    }
}