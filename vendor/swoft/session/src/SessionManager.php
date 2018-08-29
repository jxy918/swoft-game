<?php

namespace Swoft\Session;

use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Scope;
use Swoft\Session\Handler\FileSessionHandler;
use Swoft\Session\Handler\LifetimeInterface;
use Swoft\Session\Handler\RedisSessionHandler;

/**
 * @Bean(scope=Scope::PROTOTYPE)
 */
class SessionManager
{

    /**
     * The session handlers
     *
     * @var array
     */
    protected $handlers = [
        'file' => FileSessionHandler::class,
        'redis' => RedisSessionHandler::class
    ];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Create a handler by config
     *
     * @return \SessionHandlerInterface
     * @throws \InvalidArgumentException
     */
    public function createHandlerByConfig(): \SessionHandlerInterface
    {
        if (!isset($this->getConfig()['driver'])) {
            throw new \InvalidArgumentException('Session driver required');
        }
        $handler = $this->getHandler($this->getConfig()['driver']);
        $handler instanceof LifetimeInterface && $handler->setLifetime($this->getConfig()['lifetime']);
        return $handler;
    }

    /**
     * Get a handler by name
     *
     * @param string $name
     * @return \SessionHandlerInterface
     * @throws \InvalidArgumentException
     */
    public function getHandler(string $name): \SessionHandlerInterface
    {
        $name = strtolower($name);
        $this->isValidate($name);
        return App::getBean($this->handlers[$name]);
    }

    /**
     * @param string $name
     * @throws \InvalidArgumentException
     */
    protected function isValidate(string $name)
    {
        if (!array_key_exists($name, $this->handlers)) {
            throw new \InvalidArgumentException('Invalid session handler');
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return SessionManager
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @param SessionInterface $session
     * @return static
     */
    public function setSession($session): self
    {
        $this->session = $session;
        return $this;
    }

}