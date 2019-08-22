<?php declare(strict_types=1);

namespace Swoft\Console;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Contract\ConsoleErrorHandlerInterface;
use Swoft\Console\Exception\ConsoleErrorException;
use Swoft\Console\Helper\Show;
use Swoft\Error\ErrorManager;
use Swoft\Error\ErrorType;
use Throwable;
use function output;

/**
 * Class ConsoleErrorDispatcher
 *
 * @since 2.0.3
 * @Bean()
 */
class ConsoleErrorDispatcher
{
    /**
     * @param Throwable $e
     *
     * @throws ContainerException
     */
    public function run(Throwable $e): void
    {
        // Console error
        if ($e instanceof ConsoleErrorException) {
            Show::error($e->getMessage());
            return;
        }

        /** @var ErrorManager $manager */
        $manager = Swoft::getSingleton(ErrorManager::class);

        /** @var ConsoleErrorHandlerInterface $handler */
        if ($handler = $manager->matchHandler($e, ErrorType::CLI)) {
            $handler->handle($e);
            return;
        }

        // Ensure no buffer
        output()->clearBuffer();
        output()->flush();
        output()->writef(
            "<error>(CONSOLE)%s: %s</error>\nAt %s line <cyan>%d</cyan>\n<comment>Code Trace:</comment>\n%s",
            get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()
        );
    }
}
