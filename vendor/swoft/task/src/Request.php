<?php declare(strict_types=1);


namespace Swoft\Task;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Task\Contract\RequestInterface;
use Swoft\Task\Exception\TaskException;
use Swoole\Server;
use Swoole\Server\Task as SwooleTask;

/**
 * Class Request
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class Request implements RequestInterface
{
    use PrototypeTrait;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var SwooleTask
     */
    private $task;

    /**
     * @var int
     */
    private $taskId;

    /**
     * @var string
     */
    private $taskUniqid = '';

    /**
     * @var int
     */
    private $srcWorkerId;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $method = '';

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $ext = [];


    /**
     * @param Server     $server
     * @param SwooleTask $task
     *
     * @return Request
     * @throws ReflectionException
     * @throws ContainerException
     * @throws TaskException
     */
    public static function new(Server $server = null, SwooleTask $task = null): self
    {
        $instance = self::__instance();

        $instance->server      = $server;
        $instance->taskId      = $task->id;
        $instance->srcWorkerId = $task->worker_id;
        $instance->data        = $task->data;
        $instance->task        = $task;
        $instance->taskUniqid  = Task::getUniqid($task->id);

        [
            $instance->type,
            $instance->name,
            $instance->method,
            $instance->params,
            $instance->ext
        ] = Packet::unpack($task->data);

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
     * @return int
     */
    public function getSrcWorkerId(): int
    {
        return $this->srcWorkerId;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getExt(): array
    {
        return $this->ext;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getExtKey(string $name, $default = null)
    {
        return $this->ext[$name] ?? $default;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTaskUniqid(): string
    {
        return $this->taskUniqid;
    }

    /**
     * Clear
     */
    public function clear(): void
    {
        $this->server      = null;
        $this->taskId      = null;
        $this->srcWorkerId = null;
        $this->params      = [];
        $this->ext         = [];
        $this->data        = [];
    }
}