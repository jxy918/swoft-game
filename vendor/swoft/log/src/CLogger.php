<?php declare(strict_types=1);


namespace Swoft\Log;

use function count;
use function debug_backtrace;
use Monolog\Handler\HandlerInterface;
use function sprintf;

/**
 * Console logger
 *
 * @since 2.0
 */
class CLogger extends \Monolog\Logger
{
    /**
     * @var string
     */
    protected $name = 'swoft';

    /**
     * Whether to enable console logger
     *
     * @var bool
     */
    private $enable = true;

    /**
     * The handler stack
     *
     * @var HandlerInterface[]
     */
    protected $handlers;

    /**
     * All levels
     *
     * @var array
     */
    protected static $levels = array(
        self::INFO    => 'INFO',
        self::DEBUG   => 'DEBUG',
        self::WARNING => 'WARNING',
        self::ERROR   => 'ERROR',
    );

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        parent::__construct($this->name);
    }

    /**
     * Add record
     *
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = array()): bool
    {
        if (!$this->enable) {
            return true;
        }

        $message = $this->getTrace($message);

        return parent::addRecord($level, $message, $context);
    }

    /**
     * Add debug trace
     *
     * @param string $message
     *
     * @return string
     */
    public function getTrace(string $message): string
    {
        $stackStr = '';
        $traces   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $count    = count($traces);

        if ($count >= 6) {
            $info = $traces[4];
            if (isset($info['file'], $info['class'])) {
                $class    = $info['class'];
                $lineNum  = $traces[3]['line'];
                $function = $info['function'];
                $stackStr = sprintf('%s:%s(%s)', $class, $function, $lineNum);
            }
        }

        if (!empty($stackStr)) {
            $message = sprintf('%s %s', $stackStr, $message);
        }

        return $message;
    }

    /**
     * Set handlers
     *
     * @param array $handlers
     *
     * @return $this|\Monolog\Logger
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;

        return $this;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }
}
