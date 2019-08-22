<?php declare(strict_types=1);

namespace Swoft\Error;

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\Contract\ErrorHandlerInterface;
use function count;

/**
 * Class ErrorRegister
 *
 * @since 2.0
 */
final class ErrorRegister
{
    /**
     * @var array
     * [
     *  handler class => [exception class, exception class1],
     * ]
     */
    private static $handlers = [];

    /**
     * @param string $handlerClass
     * @param array  $exceptions
     */
    public static function add(string $handlerClass, array $exceptions): void
    {
        self::$handlers[$handlerClass] = $exceptions;
    }

    /**
     * @param ErrorManager $chain
     *
     * @return int
     * @throws ContainerException
     */
    public static function register(ErrorManager $chain): int
    {
        foreach (self::$handlers as $handlerClass => $exceptions) {
            /** @var ErrorHandlerInterface $handler */
            $handler = BeanFactory::getSingleton($handlerClass);
            $type    = $handler->getType();

            foreach ($exceptions as $exceptionClass) {
                $chain->addHandler($exceptionClass, $handlerClass, $type);
            }
        }

        $count = count(self::$handlers);

        // Clear handlers
        self::$handlers = [];

        return $count;
    }
}
