<?php declare(strict_types=1);

namespace Swoft\View\Contract;

/**
 * Class ViewInterface The interface of view
 * @since 1.0
 */
interface ViewInterface
{
    public const DEFAULT_SUFFIXES = ['php', 'tpl', 'html'];

    /**
     * @param string            $view
     * @param array             $data
     * @param string|null|false $layout Override default layout file
     *
     * @return string
     */
    public function render(string $view, array $data = [], $layout = null): string;

    /**
     * @param string $view
     * @param array  $data
     * @return string
     */
    public function renderPartial(string $view, array $data = []): string;
}
