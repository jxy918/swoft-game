<?php

namespace Swoft\Task\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Task\Bean\Annotation\Scheduled;
use Swoft\Task\Bean\Annotation\Task;

/**
 * Task annotation collector
 */
class TaskCollector implements CollectorInterface
{

    /**
     * @var array
     */
    private static $tasks = [];

    /**
     * @var array
     */
    private static $crons = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return void
     */
    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        if ($objectAnnotation instanceof Task) {
            self::collectTask($className, $objectAnnotation);

            return;
        }

        if ($objectAnnotation instanceof Scheduled) {
            self::collectScheduled($className, $objectAnnotation, $methodName);

            return;
        }
    }

    /**
     * collect the annotation of task
     *
     * @param string $className
     * @param Task   $objectAnnotation
     */
    private static function collectTask(string $className, Task $objectAnnotation)
    {
        $name = $objectAnnotation->getName();
        $beanName = empty($name) ? $className : $name;
        $coroutine = $objectAnnotation->isCoroutine();

        self::$tasks['mapping'][$className] = $beanName;
        self::$tasks['task'][$beanName] = [
            $className,
            $coroutine
        ];
    }

    /**
     * collect the annotation of Scheduled
     *
     * @param string    $className
     * @param Scheduled $objectAnnotation
     * @param string    $methodName
     */
    private static function collectScheduled(string $className, Scheduled $objectAnnotation, string $methodName)
    {
        if (!isset(self::$tasks['mapping'][$className])) {
            return;
        }

        $cron     = $objectAnnotation->getCron();
        $taskName = self::$tasks['mapping'][$className];

        self::$tasks['crons'][] = [
            'cron'      => $cron,
            'task'      => $taskName,
            'method'    => $methodName,
            'className' => $className,
        ];
    }

    /**
     * @return array
     */
    public static function getCollector(): array
    {
        return self::$tasks;
    }

}