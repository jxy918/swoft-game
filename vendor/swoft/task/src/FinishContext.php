<?php declare(strict_types=1);


namespace Swoft\Task;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server;

/**
 * Class FinishContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class FinishContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var int
     */
    private $taskId;

    /**
     * @var string
     */
    private $taskData;

    /**
     * Task unique id
     *
     * @var string
     */
    private $taskUniqid = '';

    /**
     * @param Server $server
     * @param int    $taskId
     * @param string $data
     *
     * @return FinishContext
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(Server $server, int $taskId, string $data): self
    {
        $instance = self::__instance();

        $instance->server     = $server;
        $instance->taskData   = $data;
        $instance->taskId     = $taskId;
        $instance->taskUniqid = Task::getUniqid($taskId);

        return $instance;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @return string
     */
    public function getTaskData(): string
    {
        return $this->taskData;
    }

    /**
     * @return string
     */
    public function getTaskUniqid(): string
    {
        return $this->taskUniqid;
    }
}