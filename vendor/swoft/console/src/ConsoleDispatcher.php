<?php declare(strict_types=1);

namespace Swoft\Console;

use ReflectionException;
use ReflectionType;
use function srun;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Co;
use Swoft\Console\Input\Input;
use Swoft\Console\Output\Output;
use Swoft\Context\Context;
use Swoft\Contract\DispatcherInterface;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoft\SwoftEvent;
use Swoole\Runtime;
use Throwable;
use function get_class;
use function get_parent_class;

/**
 * Class ConsoleDispatcher
 *
 * @since 2.0
 * @Bean("cliDispatcher")
 */
class ConsoleDispatcher implements DispatcherInterface
{
    /**
     * @param array $params
     *
     * @return void
     * @throws ReflectionException
     * @throws Throwable
     */
    public function dispatch(...$params): void
    {
        $route = $params[0];
        // Handler info
        [$className, $method] = $route['handler'];

        // Bind method params
        $params = $this->getBindParams($className, $method);
        $object = Swoft::getSingleton($className);

        // Blocking running
        if (!$route['coroutine']) {
            $this->before(get_parent_class($object), $method);
            PhpHelper::call([$object, $method], ...$params);
            $this->after($method);
            return;
        }

        // Hook php io function
        Runtime::enableCoroutine();

        // If in unit test env, has been in coroutine.
        if (\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $this->executeByCo($object, $method, $params);
            return;
        }

        // Coroutine running
        srun(function () use ($object, $method, $params) {
            $this->executeByCo($object, $method, $params);
        });
    }

    /**
     * @param object $beanObject
     * @param string $method
     * @param array  $bindParams
     *
     * @throws Throwable
     */
    public function executeByCo($beanObject, string $method, array $bindParams): void
    {
        try {
            Context::set($ctx = ConsoleContext::new());

            $this->before($method, get_class($beanObject));

            PhpHelper::call([$beanObject, $method], ...$bindParams);

            $this->after($method);
        } catch (Throwable $e) {
            /** @var ConsoleErrorDispatcher $errDispatcher */
            $errDispatcher = BeanFactory::getSingleton(ConsoleErrorDispatcher::class);

            // Handle request error
            $errDispatcher->run($e);
        } finally {
            // Defer
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Complete
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);
        }
    }

    /**
     * Get method bounded params
     *
     * @param string $class
     * @param string $method
     *
     * @return array
     * @throws ReflectionException
     */
    private function getBindParams(string $class, string $method): array
    {
        $classInfo = Swoft::getReflection($class);
        if (!isset($classInfo['methods'][$method])) {
            return [];
        }

        // binding params
        $bindParams   = [];
        $methodParams = $classInfo['methods'][$method]['params'];

        /**
         * @var string         $name
         * @var ReflectionType $paramType
         * @var mixed          $devVal
         */
        foreach ($methodParams as [, $paramType, $devVal]) {
            // Defined type of the param
            $type = $paramType->getName();

            if ($type === Output::class) {
                $bindParams[] = \output();
            } elseif ($type === Input::class) {
                $bindParams[] = \input();
            } else {
                $bindParams[] = null;
            }
        }

        return $bindParams;
    }

    /**
     * Pre middleware
     *
     * @return array
     */
    public function preMiddleware(): array
    {
        return [];
    }

    /**
     * After middleware
     *
     * @return array
     */
    public function afterMiddleware(): array
    {
        return [];
    }

    /**
     * Before dispatch
     *
     * @param array $params
     *
     * @throws Throwable
     */
    public function before(...$params): void
    {
        // TODO ... event params
        $command = $params[0];
        Swoft::trigger(ConsoleEvent::EXECUTE_BEFORE, $command, $params[1]);
    }

    /**
     * After dispatch
     *
     * @param array $params
     *
     * @throws Throwable
     */
    public function after(...$params): void
    {
        // TODO ... event params
        $command = $params[0];

        Swoft::triggerByArray(ConsoleEvent::EXECUTE_AFTER, $command, [
            'command' => $command,
        ]);
    }
}
