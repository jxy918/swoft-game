<?php declare(strict_types=1);

namespace Swoft\Console\Advanced;

use RuntimeException;
use Swoft\Console\Console;
use Swoft\Console\Contract\FormatterInterface;
use Swoft\Stdlib\Helper\ObjectHelper;

/**
 * Class MessageFormatter - message formatter
 * @package Swoft\Console\Advanced
 */
abstract class MessageFormatter implements FormatterInterface
{
    // content align
    public const ALIGN_LEFT   = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT  = 'right';

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $config
     * @return MessageFormatter
     */
    public static function create(array $config = []): self
    {
        return new static($config);
    }

    /**
     * Formatter constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        ObjectHelper::init($this, $config);

        $this->config = $config;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function format(): string
    {
        throw new RuntimeException('Please implement the method on sub-class');
    }

    /**
     * Format and output message to steam.
     *
     * @return int
     */
    public function render(): int
    {
        return $this->display();
    }

    /**
     * Format and output message to steam.
     *
     * @return int
     */
    public function display(): int
    {
        return Console::write($this->toString());
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->format();
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
