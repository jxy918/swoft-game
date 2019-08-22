<?php declare(strict_types=1);


namespace Swoft\Process;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Log\Error;
use Swoft\Process\Contract\ProcessInterface;
use Swoft\Process\Exception\ProcessException;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoole\Process\Pool;
use Throwable;

/**
 * Class ProcessDispatcher
 *
 * @since 2.0
 *
 * @Bean()
 */
class ProcessDispatcher
{
    /**
     * Process method
     */
    public const METHOD = 'run';

    /**
     * Dispatcher
     *
     * @param Pool $pool
     * @param int  $workerId
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function dispatcher(Pool $pool, int $workerId): void
    {
        try {
            $process = $this->getProcess($workerId);
            PhpHelper::call([$process, self::METHOD], $pool, $workerId);
        } catch (Throwable $e) {
            Error::log(
                sprintf('Run process for process pool fail(%s %s %d)!', $e->getMessage(), $e->getFile(), $e->getLine())
            );
        }
    }

    /**
     * @param int $workerId
     *
     * @return ProcessInterface
     * @throws ProcessException
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function getProcess(int $workerId): ProcessInterface
    {
        $processName = ProcessRegister::getProcess($workerId);
        $process     = BeanFactory::getBean($processName);

        if (!$process instanceof ProcessInterface) {
            throw new ProcessException('Process must be instanceof ProcessInterface');
        }

        return $process;
    }
}