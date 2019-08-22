<?php declare(strict_types=1);

namespace Swoft\Smarty;

use function in_array;
use function rtrim;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\ObjectHelper;
use Swoft\Smarty\Contract\SmartyInterface;

/**
 * Class Smarty - PHP view scripts Smarty
 *
 * @since 1.0
 * @Bean("smarty")
 */
class Smarty implements SmartyInterface
{
    /**
     * @var bool open debugging
     */
    protected $debugging = true;

    /**
     * @var bool open cache
     */
    protected $caching = true;

    /**
     * @var int set cache time
     */
    protected $cacheLifetime = 120;

    /**
     * @var string set left delimiter
     */
    protected $leftDelimiter = '<!--{';

    /**
     * @var string set right delimiter
     */
    protected $rightDelimiter = '}-->';

    /**
     * @var string template storage base path
     */
    protected $templateDir = '';

    /**
     * @var string compile storage base path
     */
    protected $compileDir = '';

    /**
     * @var string cache storage base path
     */
    protected $cacheDir = '';

    /**
     * @var null init smarty object
     */
    protected $smarty = null;

    /**
     * Class constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        ObjectHelper::init($this, $config);

        $this->smarty = new \Smarty();
    }

    /**
     * init smarty view
     * @return object
     */
    public function initView(): object
    {
        $this->smarty->debugging = $this->getDebugging();
        $this->smarty->caching = $this->getCaching();
        $this->smarty->cache_lifetime = $this->getCacheLifetime();
        $this->smarty->left_delimiter = $this->getLeftDelimiter();
        $this->smarty->right_delimiter = $this->getRightDelimiter();
        $this->smarty->addTemplateDir($this->getTemplateDir());
        $this->smarty->setCompileDir($this->getCompileDir());
        $this->smarty->setCacheDir($this->getCacheDir());
        return $this->smarty;
    }

    /**
     * Get the template path
     *
     * @return string
     */
    public function getTemplateDir(): string
    {
        return $this->templateDir ? \Swoft::getAlias($this->templateDir) : '';
    }

    /**
     * Set the template path
     *
     * @param string $templateDir
     */
    public function setTemplateDir(string $templateDir): void
    {
        if ($templateDir) {
            $this->templateDir = rtrim($templateDir, '/\\') . '/';
        }
    }

    /**
     * Get the compile path
     *
     * @return string
     */
    public function getCompileDir(): string
    {
        return $this->compileDir ? \Swoft::getAlias($this->compileDir) : '';
    }

    /**
     * Set the compile path
     *
     * @param string $compileDir
     */
    public function setCompileDir(string $compileDir): void
    {
        if ($compileDir) {
            $this->compileDir = rtrim($compileDir, '/\\') . '/';
        }
    }

    /**
     * Get the cacheDir path
     *
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir ? \Swoft::getAlias($this->cacheDir) : '';
    }

    /**
     * Set the cacheDir path
     *
     * @param string $cacheDir
     */
    public function setCacheDir(string $cacheDir): void
    {
        if ($cacheDir) {
            $this->cacheDir = rtrim($cacheDir, '/\\') . '/';
        }
    }

    /**
     * Get the debugging
     *
     * @return bool
     */
    public function getDebugging(): bool
    {
        return $this->debugging ? $this->debugging : true;
    }

    /**
     * Set the che debugging
     *
     * @param bool $debugging
     */
    public function setDebugging(bool $debugging): void
    {
        $this->debugging = $debugging;
    }

    /**
     * Get the caching
     *
     * @return bool
     */
    public function getCaching(): bool
    {
        return $this->caching ? $this->caching : true;
    }

    /**
     * Set the che caching
     *
     * @param bool $caching
     */
    public function setCaching(bool $caching): void
    {
        $this->caching = $caching;
    }

    /**
     * Get the cacheLifetime
     *
     * @return int
     */
    public function getCacheLifetime(): int
    {
        return $this->cacheLifetime ? $this->cacheLifetime : 120;
    }

    /**
     * Set the che cacheLifetime
     *
     * @param int $cacheLifetime
     */
    public function setCacheLifetime(int $cacheLifetime): void
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Get the leftDelimiter
     *
     * @return string
     */
    public function getLeftDelimiter(): string
    {
        return $this->leftDelimiter ? $this->leftDelimiter : '<!--{';
    }

    /**
     * Set the che leftDelimiter
     *
     * @param string $leftDelimiter
     */
    public function setLeftDelimiter(string $leftDelimiter): void
    {
        $this->leftDelimiter = $leftDelimiter;
    }

    /**
     * Get the rightDelimiter
     *
     * @return string
     */
    public function getRightDelimiter(): string
    {
        return $this->rightDelimiter ? $this->rightDelimiter : '}-->';
    }

    /**
     * Set the che rightDelimiter
     *
     * @param string $rightDelimiter
     */
    public function setRightDelimiter(string $rightDelimiter): void
    {
        $this->rightDelimiter = $rightDelimiter;
    }
}
