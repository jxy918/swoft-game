<?php

namespace Swoft\Session;

use Swoft\Helper\ArrayHelper;
use Swoft\Helper\StringHelper;
use Swoft\Session\Handler\NeedRequestInterface;


/**
 * @uses      SessionStore
 * @version   2017年12月17日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SessionStore implements SessionInterface
{

    /**
     * The session ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The session name.
     *
     * @var string
     */
    protected $name;

    /**
     * The session attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The session handler implementation.
     *
     * @var \SessionHandlerInterface
     */
    protected $handler;

    /**
     * Session store started status.
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Create a new session instance.
     *
     * @param  string                   $name
     * @param  \SessionHandlerInterface $handler
     * @param  string|null              $id
     * @throws \RuntimeException
     */
    public function __construct($name, \SessionHandlerInterface $handler, $id = null)
    {
        $this->setId($id);
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * Get the name of the session.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the current session ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the session ID.
     *
     * @param  string $id
     * @return static
     * @throws \RuntimeException
     */
    public function setId($id)
    {
        $this->id = $this->isValidId($id) ? $id : $this->generateSessionId();
        return $this;
    }

    /**
     * Start the session, reading the data from a handler.
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function start(): bool
    {
        $this->loadSession();

        if (! $this->has('_token')) {
            $this->regenerateToken();
        }

        return $this->started = true;
    }

    /**
     * Save the session data to storage.
     *
     * @return bool
     */
    public function save()
    {
        $this->getHandler()->write($this->getId(), serialize($this->attributes));
        $this->started = false;
    }

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * Checks if a key exists.
     *
     * @param  string|array $key
     * @return bool
     */
    public function exists($key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Checks if an a key is present and not null.
     *
     * @param  string|array $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->exists($key) && null !== $this->attributes[$key];
    }

    /**
     * Get an item from the session.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return ArrayHelper::get($this->attributes, $key, $default);
    }

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param  array|string $key
     * @param  mixed $value
     * @return void
     */
    public function put($key, $value = null)
    {
        ! \is_array($key) && $key = [$key => $value];
        foreach ($key as $k => $v) {
            $k && ArrayHelper::set($this->attributes, $k, $v);
        }
    }

    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token(): string
    {
        return $this->get('_token');
    }

    /**
     * Remove an item from the session, returning its value.
     *
     * @param  string $key
     * @return mixed
     */
    public function remove($key)
    {
        return ArrayHelper::pull($this->attributes, $key);
    }

    /**
     * Remove one or many items from the session.
     *
     * @param  string|array $keys
     * @return void
     */
    public function forget($keys)
    {
        ArrayHelper::forget($this->attributes, $keys);
    }

    /**
     * Remove all of the items from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->attributes = [];
    }

    /**
     * Generate a new session ID for the session.
     *
     * @param  bool $destroy
     * @return bool
     * @throws \RuntimeException
     */
    public function migrate($destroy = false): bool
    {
        if ($destroy) {
            $this->getHandler()->destroy($this->getId());
        }
        $this->setId($this->generateSessionId());
        return true;
    }

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Get the previous URL from the session.
     *
     * @return string|null
     */
    public function previousUrl()
    {
        return $this->get('_previous.url');
    }

    /**
     * Set the "previous" URL in the session.
     *
     * @param  string $url
     * @return void
     */
    public function setPreviousUrl($url)
    {
        $this->put('_previous.url', $url);
    }

    /**
     * Get the session handler instance.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler(): \SessionHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Determine if the session handler needs a request.
     *
     * @return bool
     */
    public function handlerNeedsRequest(): bool
    {
        return $this->getHandler() instanceof NeedRequestInterface;
    }

    /**
     * Set the request on the handler instance.
     *
     * @param  \Swoft\Http\Message\Server\Request $request
     * @return void
     */
    public function setRequestOnHandler($request)
    {
        if ($this->handlerNeedsRequest()) {
            /** @var NeedRequestInterface $handler */
            $handler = $this->getHandler();
            $handler->setRequest($request);
        }
    }

    /**
     * Load the session data from the handler
     *
     * @return void
     */
    protected function loadSession()
    {
        $this->attributes = array_merge($this->attributes, $this->readFromHandler());
    }

    /**
     * @return array
     */
    protected function readFromHandler(): array
    {
        $data = $this->handler->read($this->getId());
        if ($data) {
            $data = @unserialize($data);
            if ($data === false || null === $data || ! \is_array($data)) {
                $data = [];
            }
        }
        return $data ? : [];
    }

    /**
     * Generate a new random sessoion ID
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function generateSessionId(): string
    {
        return StringHelper::random(40);
    }

    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function regenerateToken()
    {
        $this->put('_token', StringHelper::random(40));
    }

    /**
     * Determine if this is valid session ID.
     *
     * @param $id
     * @return bool
     */
    protected function isValidId($id): bool
    {
        return \is_string($id) && ctype_alnum($id) && \strlen($id) === 40;
    }

}