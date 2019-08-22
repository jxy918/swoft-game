<?php declare(strict_types=1);

namespace Swoft\Config;

use InvalidArgumentException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Config\Contract\ParserInterface;
use Swoft\Config\Exception\ConfigException;
use Swoft\Config\Parser\PhpParser;
use Swoft\Config\Parser\YamlParser;
use Swoft\Stdlib\Collection;
use Swoft\Stdlib\Helper\ArrayHelper;

/**
 * Class Config
 *
 * @Bean("config")
 *
 * @since 2.0
 */
class Config extends Collection
{

    /**
     * Php formatter
     */
    public const TYPE_PHP = 'php';

    /**
     * Yaml formatter
     */
    public const TYPE_YAML = 'yaml';

    /**
     * Base file name
     *
     * @var string
     */
    private $base = 'base';

    /**
     * Default config type
     *
     * @var string
     */
    private $type = self::TYPE_PHP;

    /**
     * @var string
     */
    private $env = '';

    /**
     * Config path
     *
     * @var string
     */
    private $path = '';

    /**
     * Resource parsers
     *
     * @var array
     */
    private $parsers = [];

    /**
     * Init
     */
    public function init(): void
    {
        $parsers = $this->getConfigParser();
        if (!isset($parsers[$this->type])) {
            throw new InvalidArgumentException('Resource is not exist! resourceType=' . $this->type);
        }

        /* @var ParserInterface $parser */
        $parser    = $parsers[$this->type];
        $parseData = $parser->parse($this);

        $this->items = $parseData;
    }

    /**
     * Get value by key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key = null, $default = null)
    {
        return ArrayHelper::get($this->items, $key, $default);
    }

    /**
     * Get value
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return ArrayHelper::get($this->items, $key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @throws ConfigException
     */
    public function offsetSet($key, $value): void
    {
        throw new ConfigException('Config is not supported offsetSet!');
    }

    /**
     * @param string $key
     *
     * @throws ConfigException
     */
    public function offsetUnset($key): void
    {
        throw new ConfigException('Config is not supported offsetUnset!');
    }

    /**
     * @param array|string $keys
     *
     * @return Collection
     * @throws ConfigException
     */
    public function forget($keys): Collection
    {
        throw new ConfigException('Config is not supported forget!');
    }

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return ArrayHelper::has($this->items, $key);
    }

    /**
     * @param string $base
     */
    public function setBase(string $base): void
    {
        $this->base = $base;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @param array $parsers
     */
    public function setParsers(array $parsers): void
    {
        $this->parsers = $parsers;
    }

    /**
     * @param string $env
     */
    public function setEnv(string $env): void
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * Get parse resourcers
     *
     * @return array
     */
    private function getConfigParser(): array
    {
        $parsers = $this->parsers;

        // Set default parser
        if (empty($this->parsers)) {
            $parsers = [
                self::TYPE_PHP  => new PhpParser(),
                self::TYPE_YAML => new YamlParser(),
            ];
        }

        return $parsers;
    }
}
