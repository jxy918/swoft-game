<?php declare(strict_types=1);

namespace Swoft\Stdlib\Helper;

use const FILEINFO_MIME_TYPE;
use function finfo_file;
use function finfo_open;
use function function_exists;
use function ltrim;
use function pathinfo;
use const PATHINFO_EXTENSION;
use function strrchr;

/**
 * Class FileHelper
 */
class FileHelper extends FSHelper
{
    /**
     * Get file extension, suffix name
     * @param string $filename
     * @param bool $clearPoint
     * @return string
     */
    public static function getSuffix(string $filename, bool $clearPoint = false): string
    {
        if (!$suffix = strrchr($filename, '.')) {
            return '';
        }

        return $clearPoint ? ltrim($suffix, '.') : $suffix;
    }

    /**
     * @param string $path
     * @param bool   $clearPoint
     *
     * @return string
     */
    public static function getExt(string $path, bool $clearPoint = false): string
    {
        return self::getExtension($path, $clearPoint);
    }

    /**
     * Get file extension, suffix name
     *
     * @param string $path
     * @param bool $clearPoint
     * @return string
     */
    public static function getExtension(string $path, bool $clearPoint = false): string
    {
        if ($ext = pathinfo($path, PATHINFO_EXTENSION)) {
            return $clearPoint ? $ext : '.' . $ext;
        }

        return '';
    }

    /**
     * @param string $file
     * @return string eg: image/gif
     */
    public static function mimeType(string $file): string
    {
        if (function_exists('finfo_file')) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        }

        return '';
    }
}
