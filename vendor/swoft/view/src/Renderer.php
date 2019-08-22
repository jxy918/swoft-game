<?php declare(strict_types=1);

namespace Swoft\View;

use function in_array;
use function rtrim;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\FileHelper;
use Swoft\Stdlib\Helper\ObjectHelper;
use Swoft\View\Contract\ViewInterface;

/**
 * Class Renderer - PHP view scripts renderer
 *
 * @since 1.0
 * @Bean("view")
 */
class Renderer implements ViewInterface
{
    /**
     * @var string Default layout file
     */
    protected $layout = '';

    /**
     * @var string Default view suffix.
     */
    protected $suffix = 'php';

    /**
     * @var array Allowed suffix list. It use auto add suffix.
     */
    protected $suffixes = [];

    /**
     * @var string View storage base path
     */
    protected $viewsPath = '';

    /**
     * @var array Attributes for the view
     */
    protected $attributes = [];

    /**
     * In layout file '...<body>{_CONTENT_}</body>...'
     *
     * @var string
     */
    protected $placeholder = '{_CONTENT_}';

    /**
     * Class constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->suffixes = self::DEFAULT_SUFFIXES;

        ObjectHelper::init($this, $config);
    }

    /**
     * Render a view, if layout file is setting, will use it.
     * throws RuntimeException if view file does not exist
     *
     * @param string            $view
     * @param array             $data   extract data to view, cannot contain view as a key
     * @param string|null|false $layout Override default layout file
     * @return string
     * @throws \Throwable
     */
    public function render(string $view, array $data = [], $layout = null): string
    {
        $output = $this->fetch($view, $data);

        // False - will disable use layout file
        if ($layout === false) {
            return $output;
        }

        return $this->renderContent($output, $data, $layout);
    }

    /**
     * @param string $view
     * @param array  $data
     * @return string
     * @throws \Throwable
     */
    public function renderPartial(string $view, array $data = []): string
    {
        return $this->fetch($view, $data);
    }

    /**
     * @param string      $content
     * @param array       $data
     * @param string|null $layout override default layout file
     * @return string
     * @throws \Throwable
     */
    public function renderBody(string $content, array $data = [], $layout = null): string
    {
        return $this->renderContent($content, $data, $layout);
    }

    /**
     * @param string      $content
     * @param array       $data
     * @param string|null $layout override default layout file
     * @return string
     * @throws \Throwable
     */
    public function renderContent(string $content, array $data = [], $layout = null): string
    {
        // Render layout
        if ($layout = $layout ?: $this->layout) {
            $mark    = $this->placeholder;
            $main    = $this->fetch($layout, $data);
            $content = \preg_replace("/$mark/", $content, $main, 1);
        }

        return $content;
    }

    /**
     * @param string $view
     * @param array  $data
     * @param bool   $outputIt
     * @return string
     * @throws \Throwable
     */
    public function include(string $view, array $data = [], $outputIt = true): string
    {
        if ($outputIt) {
            echo $this->fetch($view, $data);
            return '';
        }

        return $this->fetch($view, $data);
    }

    /**
     * Renders a view and returns the result as a string
     * throws RuntimeException if $viewsPath . $view does not exist
     *
     * @param string $view
     * @param array  $data
     * @return mixed
     * @throws \Throwable
     */
    public function fetch(string $view, array $data = [])
    {
        $file = $this->getViewFile($view);

        if (!is_file($file)) {
            throw new \RuntimeException("cannot render '$view' because the view file does not exist. File: $file");
        }

        /*
        foreach ($data as $k=>$val) {
            if (in_array($k, array_keys($this->attributes))) {
                throw new \InvalidArgumentException(
                    "Duplicate key found in data and renderer attributes. " . $k
                );
            }
        }
        */
        $data = \array_merge($this->attributes, $data);

        try {
            \ob_start();
            $this->protectedIncludeScope($file, $data);
            $output = \ob_get_clean();
        } catch (\Throwable $e) { // PHP 7+
            \ob_end_clean();
            throw $e;
        }

        return $output;
    }

    /**
     * @param $view
     * @return string
     */
    public function getViewFile(string $view): string
    {
        $view = $this->getRealView($view);

        return FileHelper::isAbsPath($view) ? $view : $this->getViewsPath() . $view;
    }

    /**
     * @param string $file
     * @param array  $data
     */
    protected function protectedIncludeScope($file, array $data): void
    {
        \extract($data, EXTR_OVERWRITE);
        include $file;
    }

    /**
     * Get the attributes for the renderer
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the attributes for the renderer
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Add an attribute
     *
     * @param $key
     * @param $value
     */
    public function addAttribute($key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Retrieve an attribute
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (!isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * Get the view path
     *
     * @return string
     */
    public function getViewsPath(): string
    {
        return $this->viewsPath ? \Swoft::getAlias($this->viewsPath) : '';
    }

    /**
     * Set the view path
     *
     * @param string $viewsPath
     */
    public function setViewsPath(string $viewsPath): void
    {
        if ($viewsPath) {
            $this->viewsPath = rtrim($viewsPath, '/\\') . '/';
        }
    }

    /**
     * Get the layout file
     *
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Set the layout file
     *
     * @param string $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = rtrim($layout, '/\\');
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @param string $view
     * @return string
     */
    protected function getRealView(string $view): string
    {
        $sfx = FileHelper::getSuffix($view, true);
        $ext = $this->suffix;

        // No ext. eg: 'home/index'
        if ($sfx === '') {
            return $view . '.' . $ext;
        }

        if ($sfx === $ext || in_array($sfx, $this->suffixes, true)) {
            return $view;
        }

        return $view . '.' . $ext;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
    }
}
