<?php declare(strict_types=1);


namespace Swoft\Log\Handler;


use DateTime;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use function sprintf;
use Swoft\Log\Logger as SwoftLogger;
use Toolkit\Cli\Color;
use Toolkit\Cli\ColorTag;

/**
 * Class CEchoHandler
 *
 * @since 2.0
 */
class CEchoHandler extends AbstractProcessingHandler
{
    /**
     * All styles
     */
    public const STYLES = [
        Logger::INFO    => 'green',
        Logger::DEBUG   => 'cyan',
        Logger::WARNING => 'yellow',
        Logger::ERROR   => 'red',
    ];

    /**
     * Write log levels
     *
     * @var string
     */
    protected $levels = '';

    /**
     * Write log to command line
     *
     * @var bool
     */
    protected $output = true;

    /**
     * @var array
     */
    protected $levelValues = [];

    /**
     * Output message to command line
     *
     * @param array $record
     */
    protected function write(array $record): void
    {
        if (!$this->output) {
            return;
        }

        /* @var DateTime $datetime */
        $datetime = $record['datetime'];
        $time     = $datetime->format('Y/m/d-H:i:s');

        // Record message
        $level     = $record['level'];
        $message   = $record['message'];
        $levelName = $record['level_name'];
        $levelName = sprintf('[%s]', $levelName);

        // Add level tag
        $tagName   = self::STYLES[$level] ?? 'info';
        $levelName = ColorTag::add($levelName, $tagName);

        // Format message
        $message = sprintf('%s %s %s' . PHP_EOL, $time, $levelName, $message);

        echo Color::render($message);
    }

    /**
     * @param string $levels
     */
    public function setLevels(string $levels): void
    {
        $this->levels = $levels;

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
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    /**
     * @param bool $output
     */
    public function setOutput(bool $output): void
    {
        $this->output = $output;
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    public function isHandling(array $record)
    {
        if (empty($this->levelValues)) {
            return true;
        }

        return in_array($record['level'], $this->levelValues, true);
    }
}