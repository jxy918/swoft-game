<?php declare(strict_types=1);


namespace Swoft\Task;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Log\Helper\CLog;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Router\Router;
use Throwable;

/**
 * Class SyncTaskDispatcher
 *
 * @since 2.0
 *
 * @Bean()
 */
class SyncTaskDispatcher
{
    /**
     * Dispatch
     *
     * @param string $type
     * @param string $name
     * @param string $method
     * @param array  $params
     * @param array  $ext
     *
     * @return string
     */
    public function dispatch(string $type, string $name, string $method, array $params, array $ext): string
    {
        try {
            $result   = $this->handle($name, $method, $params);
            $response = Packet::packResponse($result);
        } catch (Throwable $e) {
            $response = Packet::packResponse(null, $e->getCode(), $e->getMessage());
            CLog::error('Sync task fail(%s %s %d)!', $e->getMessage(), $e->getFile(), $e->getLine());
        }

        return $response;
    }

    /**
     * Handle task
     *
     * @param string $name
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     * @throws TaskException
     */
    private function handle(string $name, string $method, array $params)
    {
        /* @var Router $router */
        $router = BeanFactory::getBean('taskRouter');

        $match = $router->match($name, $method);
        [$status, $handler] = $match;

        if ($status != Router::FOUND || empty($handler)) {
            throw new TaskException(
                sprintf('Task(name=%s method=%s) is not exist!', $name, $method)
            );
        }

        [$className, $methodName] = $handler;
        $object = BeanFactory::getBean($className);
        if (!method_exists($object, $methodName)) {
            throw new TaskException(
                sprintf('Task(name=%s method=%s) method is not exist!', $name, $method)
            );
        }

        return PhpHelper::call([$object, $methodName], ... $params);
    }
}