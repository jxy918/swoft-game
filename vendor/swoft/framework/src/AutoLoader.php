<?php declare(strict_types=1);

namespace Swoft;

use function alias;
use function bean;
use Monolog\Formatter\LineFormatter;
use Swoft\Config\Config;
use Swoft\Annotation\AutoLoader as AnnotationAutoLoader;
use Swoft\Contract\DefinitionInterface;
use Swoft\Event\Manager\EventManager;
use Swoft\Log\Handler\FileHandler;
use Swoft\Log\Logger;
use Throwable;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends AnnotationAutoLoader implements DefinitionInterface
{
    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * Core bean definition
     *
     * @return array
     * @throws Throwable
     */
    public function beans(): array
    {
        return [
            'config'             => [
                'class'   => Config::class,
                'path'    => alias('@config'),
                'parsers' => [
                    Config::TYPE_PHP => '${phpParser}'
                ]
            ],
            'eventManager'       => [
                'class' => EventManager::class,
            ],
            'lineFormatter'      => [
                'class'      => LineFormatter::class,
                'format'     => '%datetime% [%level_name%] [%channel%] [%event%] [tid:%tid%] [cid:%cid%] [traceid:%traceid%] [spanid:%spanid%] [parentid:%parentid%] %messages%',
                'dateFormat' => 'Y-m-d H:i:s',
            ],
            'noticeHandler'      => [
                'class'     => FileHandler::class,
                'logFile'   => '@runtime/logs/notice.log',
                'formatter' => bean('lineFormatter'),
                'levels'    => 'notice,info,debug,trace',
            ],
            'applicationHandler' => [
                'class'     => FileHandler::class,
                'logFile'   => '@runtime/logs/error.log',
                'formatter' => bean('lineFormatter'),
                'levels'    => 'error,warning',
            ],
            'logger'             => [
                'class'        => Logger::class,
                'flushRequest' => false,
                'enable'       => false,
                'handlers'     => [
                    'application' => bean('applicationHandler'),
                    'notice'      => bean('noticeHandler'),
                ],
            ]
        ];
    }
}