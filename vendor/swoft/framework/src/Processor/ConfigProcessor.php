<?php declare(strict_types=1);

namespace Swoft\Processor;

use function define;
use function env;

/**
 * Config processor
 *
 * @since 2.0
 */
class ConfigProcessor extends Processor
{
    /**
     * Handle config
     */
    public function handle(): bool
    {
        // Define constant
        $this->defineConstant();

        if (!$this->application->beforeConfig()) {
            return false;
        }

        return $this->application->afterConfig();
    }

    /**
     * Define constant
     */
    protected function defineConstant(): void
    {
        // Define some global constants
        define('APP_DEBUG', (int)env('APP_DEBUG', 0));
        define('SWOFT_DEBUG', (int)env('SWOFT_DEBUG', 0));
    }
}
