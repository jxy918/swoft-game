<?php declare(strict_types=1);

namespace Swoft\View;

/**
 * Class ViewRegister
 *
 * @since 2.0
 */
class ViewRegister
{
    /**
     * @var array
     */
    private static $views = [];

    /**
     * @param string $class
     * @param string $method
     * @param string $template
     * @param string $layout
     */
    public static function bindView(string $class, string $method, string $template, string $layout): void
    {
        $actionId = $class . '@' . $method;

        // Storage template info
        self::$views[$actionId] = [$template, $layout];
    }

    /**
     * @return array
     */
    public static function getViews(): array
    {
        return self::$views;
    }

    /**
     * @param string $actionId
     *
     * @return array
     */
    public static function findBindView(string $actionId): array
    {
        return self::$views[$actionId] ?? [];
    }
}
