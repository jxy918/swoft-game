<?php declare(strict_types=1);

namespace Swoft\Console\Advanced;

use RuntimeException;

/**
 * Class NotifyMessage - Notifier like progress, spinner ....
 *
 * @since 2.0
 * @link https://github.com/wp-cli/php-cli-tools/tree/master/lib/cli
 */
class NotifyMessage
{
    /** @var int Speed value. allow 1 - 10 */
    protected $speed = 2;

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        return $this->speed;
    }

    /**
     * @param int $speed
     */
    public function setSpeed($speed): void
    {
        $this->speed = (int)$speed;
    }

    public function display(): void
    {
        throw new RuntimeException('Please implement the method on sub-class');
    }
}
