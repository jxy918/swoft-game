<?php declare(strict_types=1);

namespace Swoft\Console\Advanced\Formatter;

use Swoft\Console\Advanced\MessageFormatter;
use Swoft\Console\Console;
use Swoft\Stdlib\Helper\Arr;
use Toolkit\Cli\ColorTag;
use function array_merge;
use function str_pad;
use function trim;
use function ucfirst;

/**
 * Class Padding
 * @package Swoft\Console\Advanced\Formatter
 */
class Padding extends MessageFormatter
{
    /**
     * ```php
     * $data = [
     *  'Eggs' => '$1.99',
     *  'Oatmeal' => '$4.99',
     *  'Bacon' => '$2.99',
     * ];
     * ```
     *
     * @param array  $data
     * @param string $title
     * @param array  $opts
     */
    public static function show(array $data, string $title = '', array $opts = []): void
    {
        if (!$data) {
            return;
        }

        $string = $title ? ColorTag::wrap(ucfirst($title), 'comment') . ":\n" : '';
        $opts   = array_merge([
            'char'       => '.',
            'indent'     => '  ',
            'padding'    => 10,
            'valueStyle' => 'info',
        ], $opts);

        $keyMaxLen  = Arr::getKeyMaxWidth($data);
        $paddingLen = $keyMaxLen > $opts['padding'] ? $keyMaxLen : $opts['padding'];

        foreach ($data as $label => $value) {
            $value  = ColorTag::wrap((string)$value, $opts['valueStyle']);
            $string .= $opts['indent'] . str_pad($label, $paddingLen, $opts['char']) . " $value\n";
        }

        Console::write(trim($string));
    }
}
