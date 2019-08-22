<?php declare(strict_types=1);

namespace Swoft\Error;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Throwable;
use function count;
use function get_class;

/**
 * Class ErrorManager
 *
 * @since 2.0
 *
 * @Bean()
 */
class ErrorManager
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     * [
     *  exception class => handler class,
     *  ... ...
     * ]
     */
    private $handlers = [];

    /**
     * Add a handler class to chains
     *
     * @param string $exceptionClass
     * @param string $handlerClass
     * @param int    $type
     */
    public function addHandler(string $exceptionClass, string $handlerClass, int $type = ErrorType::DEF): void
    {
        $this->count++;
        $this->handlers[$type][$exceptionClass] = $handlerClass;
    }

    /**
     * @param Throwable $e
     * @param int       $type
     *
     * @return mixed|null
     * @throws ContainerException
     */
    public function matchHandler(Throwable $e, int $type = ErrorType::DEF)
    {
        // No handlers found
        if (!isset($this->handlers[$type]) || $this->getCount() === 0) {
            return null;
        }

        $errClass = get_class($e);
        $handlers = $this->handlers[$type];

        if (isset($handlers[$errClass])) {
            return Swoft::getSingleton($handlers[$errClass]);
        }

        $handler = null;

        foreach ($handlers as $exceptionClass => $handlerClass) {
            if ($e instanceof $exceptionClass) {
                $handler = Swoft::getSingleton($handlerClass);
                break;
            }
        }

        return $handler;
    }

    /**
     * @return int
     */
    public function getTypeCount(): int
    {
        return count($this->handlers);
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Clear handler chains
     *
     * @return void
     */
    public function clear(): void
    {
        $this->count    = 0;
        $this->handlers = [];
    }
}
