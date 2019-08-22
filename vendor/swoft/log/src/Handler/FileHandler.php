<?php declare(strict_types=1);


namespace Swoft\Log\Handler;


use function alias;
use function array_column;
use DateTime;
use function dirname;
use function implode;
use function in_array;
use InvalidArgumentException;
use function is_dir;
use Monolog\Handler\AbstractProcessingHandler;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Log\Helper\Log;
use Swoft\Log\Logger;
use Swoft\Log\Logger as SwoftLogger;
use Swoft\Stdlib\Helper\JsonHelper;
use UnexpectedValueException;

/**
 * Class FileHandler
 *
 * @since 2.0
 */
class FileHandler extends AbstractProcessingHandler
{
    /**
     * Write log levels
     *
     * @var string
     */
    protected $levels = '';

    /**
     * Write log file
     *
     * @var string
     */
    protected $logFile = '';

    /**
     * @var array
     */
    protected $levelValues = [];

    /**
     * Will exec on construct
     */
    public function init(): void
    {
        $this->logFile = alias($this->logFile);
        $this->createDir();

        if (is_array($this->levels)) {
            $this->levelValues = $this->levels;
            return;
        }

        // Levels like 'notice,error'
        if (is_string($this->levels)) {
            $levelNames        = explode(',', $this->levels);
            $this->levelValues = SwoftLogger::getLevelByNames($levelNames);
        }
    }

    /**
     * Write log by batch
     *
     * @param array $records
     *
     * @return void
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handleBatch(array $records): void
    {
        $records = $this->recordFilter($records);
        if (!$records) {
            return;
        }

        $this->write($records);
    }

    /**
     * Write file
     *
     * @param array $records
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    protected function write(array $records): void
    {
        if (Log::getLogger()->isJson()) {
            $records = array_map([$this, 'formatJson'], $records);
        } else {
            $records = array_column($records, 'formatted');
        }

        $messageText = implode("\n", $records) . "\n";

        if (Co::id() <= 0) {
            throw new InvalidArgumentException('Write log file must be under Coroutine!');
        }

        $res = Co::writeFile($this->logFile, $messageText, FILE_APPEND);

        if ($res === false) {
            throw new InvalidArgumentException(
                sprintf('Unable to append to log file: %s', $this->logFile)
            );
        }
    }

    /**
     * Filter record
     *
     * @param array $records
     *
     * @return array
     */
    private function recordFilter(array $records): array
    {
        $messages = [];
        foreach ($records as $record) {
            if (!isset($record['level'])) {
                continue;
            }
            if (!$this->isHandling($record)) {
                continue;
            }

            $record              = $this->processRecord($record);
            $record['formatted'] = $this->getFormatter()->format($record);

            $messages[] = $record;
        }
        return $messages;
    }

    /**
     * @param array $record
     *
     * @return string
     */
    public function formatJson(array $record): string
    {
        unset($record['formatted'], $record['context'], $record['extra']);

        if ($record['datetime'] instanceof DateTime) {
            $record['datetime'] = $record['datetime']->format('Y-m-d H:i');
        }
        return JsonHelper::encode($record, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Create dir
     */
    private function createDir(): void
    {
        $logDir = dirname($this->logFile);

        if ($logDir !== null && !is_dir($logDir)) {
            $status = mkdir($logDir, 0777, true);
            if ($status === false) {
                throw new UnexpectedValueException(
                    sprintf('There is no existing directory at "%s" and its not buildable: ', $logDir)
                );
            }
        }
    }

    /**
     * Whether to handler log
     *
     * @param array $record
     *
     * @return bool
     */
    public function isHandling(array $record): bool
    {
        if (empty($this->levelValues)) {
            return true;
        }

        return in_array($record['level'], $this->levelValues, true);
    }
}