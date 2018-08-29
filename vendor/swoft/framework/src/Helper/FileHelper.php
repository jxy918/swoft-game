<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-24
 * Time: 11:11
 */

namespace Swoft\Helper;

/**
 * Class FileHelper
 * @package Swoft\Helper
 */
class FileHelper
{
    /**
     * 获得文件扩展名、后缀名
     * @param $filename
     * @param bool $clearPoint 是否带点
     * @return string
     */
    public static function getSuffix($filename, $clearPoint = false): string
    {
        $suffix = strrchr($filename, '.');

        return (bool)$clearPoint ? trim($suffix, '.') : $suffix;
    }

    /**
     * @param $path
     * @return bool
     */
    public static function isAbsPath($path): bool
    {
        if (!$path || !\is_string($path)) {
            return false;
        }

        if (
            $path{0} === '/' ||  // linux/mac
            1 === \preg_match('#^[a-z]:[\/|\\\]{1}.+#i', $path) // windows
        ) {
            return true;
        }

        return false;
    }

    /**
     * md5 of dir
     *
     * @param string $dir
     *
     * @return bool|string
     */
    public static function md5File($dir)
    {
        if (!is_dir($dir)) {
            return '';
        }

        $md5File = array();
        $d       = dir($dir);
        while (false !== ($entry = $d->read())) {
            if ($entry !== '.' && $entry !== '..') {
                if (is_dir($dir . '/' . $entry)) {
                    $md5File[] = self::md5File($dir . '/' . $entry);
                } elseif (substr($entry, -4) === '.php') {
                    $md5File[] = md5_file($dir . '/' . $entry);
                }
                $md5File[] = $entry;
            }
        }
        $d->close();

        return md5(implode('', $md5File));
    }
}
