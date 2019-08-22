<?php declare(strict_types=1);

namespace Swoft\Http\Server\Router;

use LogicException;
use function trim;

/**
 * Trait RouterConfigTrait
 *
 * @since 2.0
 */
trait RouterConfigTrait
{
    /** @var string The router name */
    private $name = '';

    /**
     * some available patterns regex
     * $router->get('/user/{id}', 'handler');
     * @var array
     */
    protected static $globalParams = [
        'all' => '.*',
        'any' => '[^/]+',        // match any except '/'
        'num' => '[1-9][0-9]*',  // match a number and gt 0
        'int' => '\d+',          // match a number
        'act' => '[a-zA-Z][\w-]+', // match a action name
    ];

    /** @var array global Options */
    private $globalOptions = [
        // 'domains' => [ 'localhost' ], // allowed domains
        // 'schemas' => [ 'http' ], // allowed schemas
        // 'time' => ['12'],
    ];

    /*******************************************************************************
     * router config
     ******************************************************************************/

    /**
     * Ignore last slash char('/'). If is True, will clear last '/'.
     * @var bool
     */
    public $ignoreLastSlash = false;

    /**
     * The param route cache number.
     * @var int
     */
    public $tmpCacheNumber = 500;

    /**
     * whether handle method not allowed. If True, will find allowed methods.
     * @var bool
     */
    public $handleMethodNotAllowed = false;

    /**
     * Controller suffix. eg: 'Controller'
     * @var string
     */
    public $controllerSuffix = 'Controller';

    /**
     * config the router
     * @param array $config
     * @throws LogicException
     */
    public function config(array $config): void
    {
        if ($this->routeCounter > 0) {
            throw new LogicException('Routing has been added, and configuration is not allowed!');
        }

        $props = [
            'name'                   => 1,
            'chains'                 => 1,
            'ignoreLastSlash'        => 1,
            'tmpCacheNumber'         => 1,
            'handleMethodNotAllowed' => 1,
            // 'autoRoute'              => 1,
            // 'controllerNamespace'    => 1,
            'controllerSuffix'       => 1,
        ];

        foreach ($config as $name => $value) {
            if (isset($props[$name])) {
                $this->$name = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param array $params
     */
    public function addGlobalParams(array $params): void
    {
        foreach ($params as $name => $pattern) {
            $this->addGlobalParam($name, $pattern);
        }
    }

    /**
     * @param string $name
     * @param string $pattern
     */
    public function addGlobalParam(string $name, string $pattern): void
    {
        $name = trim($name, '{} ');
        // add
        self::$globalParams[$name] = $pattern;
    }

    /**
     * @return array
     */
    public function getGlobalParams(): array
    {
        return self::$globalParams;
    }

    /**
     * @return array
     */
    public function getGlobalOptions(): array
    {
        return $this->globalOptions;
    }

    /**
     * @param array $globalOptions
     */
    public function setGlobalOptions(array $globalOptions): void
    {
        $this->globalOptions = $globalOptions;
    }
}
