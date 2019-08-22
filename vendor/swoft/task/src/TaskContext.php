<?php declare(strict_types=1);


namespace Swoft\Task;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;

/**
 * Class TaskContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class TaskContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $repsonse;

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return TaskContext
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(Request $request, Response $response): self
    {
        $intance = self::__instance();

        $intance->request  = $request;
        $intance->repsonse = $response;

        $intance->setMulti($request->getExt());
        return $intance;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getRepsonse(): Response
    {
        return $this->repsonse;
    }

    /**
     * Clear
     */
    public function clear(): void
    {
        $this->request  = null;
        $this->repsonse = null;
    }
}