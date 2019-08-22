<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-01-07
 * Time: 23:52
 */

namespace Toolkit\Cli;

use function preg_match_all;
use function preg_replace;
use function strpos;

/**
 * Class ColorTag
 *
 * @package Toolkit\Cli
 */
class ColorTag
{
    // regex used for removing color tags
    public const STRIP_TAG = '/<[\/]?[a-zA-Z=;]+>/';

    // Regex to match tags/
    public const MATCH_TAG = '/<([a-zA-Z=;_]+)>(.*?)<\/\\1>/s';

    /**
     * Alias of the wrap()
     *
     * @param string $text
     * @param string $tag
     *
     * @return string
     */
    public static function add(string $text, string $tag): string
    {
        return self::wrap($text, $tag);
    }

    /**
     * wrap a color style tag
     *
     * @param string $text
     * @param string $tag
     *
     * @return string
     */
    public static function wrap(string $text, string $tag): string
    {
        if (!$text || !$tag) {
            return $text;
        }

        return "<$tag>$text</$tag>";
    }

    /**
     * @param string $text
     *
     * @return array
     */
    public static function matchAll(string $text): array
    {
        if (!preg_match_all(self::MATCH_TAG, $text, $matches)) {
            return [];
        }

        return $matches;
    }

    public static function parse(string $text): string
    {
        return '';
    }

    /**
     * Exists color tags
     *
     * @param string $text
     *
     * @return bool
     */
    public static function exists(string $text): bool
    {
        return strpos($text, '</') > 0;
    }

    /**
     * Alias of the strip()
     *
     * @param string $text
     *
     * @return string
     */
    public static function clear(string $text): string
    {
        return self::strip($text);
    }

    /**
     * Strip color tags from a string.
     *
     * @param string $text
     *
     * @return mixed
     */
    public static function strip(string $text): string
    {
        if (false === strpos($text, '</')) {
            return $text;
        }

        // $text = \strip_tags($text);
        return preg_replace(self::STRIP_TAG, '', $text);
    }
}
