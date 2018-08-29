<?php

namespace Swoft\Session\Handler;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;


/**
 * Class RedisSessionHandler
 *
 * @Bean()
 * @package Swoft\Session\Handler
 */
class RedisSessionHandler implements \SessionHandlerInterface, LifetimeInterface
{

    use LifetimeTrait;

    /**
     * @var string
     */
    private $prefix = 'swoft_session';

    /**
     * @var string
     */
    private $glue = ':';

    /**
     * @Inject()
     * @var \Swoft\Redis\Redis
     */
    private $redis;

    /**
     * RedisSessionHandler constructor.
     *
     * @param int $lifetime
     */
    public function __construct($lifetime = 15 * 60)
    {
        $this->setLifetime($lifetime);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        return (bool)$this->redis->delete($this->key($sessionId));
    }

    /**
     * @inheritdoc
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $name)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($sessionId)
    {
        $value = $this->redis->get($this->key($sessionId));
        return $value ? $this->unserialize($value) : '';
    }

    /**
     * @inheritdoc
     */
    public function write($sessionId, $data)
    {
        return (bool)$this->redis->set($this->key($sessionId), $this->serialize($data), $this->getLifetime());
    }

    /**
     * @param string $sessionId
     * @return string
     */
    protected function key(string $sessionId): string
    {
        return implode($this->glue, [$this->prefix, $sessionId]);
    }

    /**
     * Serialize the value
     *
     * @param $value
     * @return int|string
     */
    protected function serialize($value)
    {
        return is_numeric($value) ? $value : serialize($value);
    }

    /**
     * Unserialize the value
     *
     * @param $value
     * @return int|string
     */
    protected function unserialize($value)
    {
        return is_numeric($value) ? $value : unserialize($value, []);
    }

}
