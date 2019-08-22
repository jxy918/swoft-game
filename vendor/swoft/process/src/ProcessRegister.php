<?php declare(strict_types=1);


namespace Swoft\Process;

use Swoft\Process\Annotation\Mapping\Process as ProcessAnnotation;
use Swoft\Process\Exception\ProcessException;

/**
 * Class ProcessRegister
 *
 * @since 2.0
 */
class ProcessRegister
{
    /**
     * @var array
     * @example
     * [
     *     'workerId' => [
     *          'className' => 'className',
     *      ]
     * ]
     */
    private static $process = [];

    /**
     * @param string $className
     * @param array  $workerIds
     *
     * @throws ProcessException
     */
    public static function registerProcess(string $className, array $workerIds): void
    {
        foreach ($workerIds as $workerId) {
            if (isset(self::$process[$workerId])) {
                throw new ProcessException(sprintf('Worker process(%d) for process pool must be only one!', $workerId));
            }

            self::$process[$workerId]['class'] = $className;
        }
    }

    /**
     * @param int $workerId
     *
     * @return string
     * @throws ProcessException
     */
    public static function getProcess(int $workerId): string
    {
        $className = self::$process[$workerId]['class'] ?? '';
        if (!empty($className)) {
            return $className;
        }

        $default   = ProcessAnnotation::DEFAULT;
        $className = self::$process[$default]['class'] ?? '';
        if (empty($className)) {
            throw new ProcessException(sprintf('Worker process(%d) for process pool must be defined!', $workerId));
        }

        return $className;
    }
}