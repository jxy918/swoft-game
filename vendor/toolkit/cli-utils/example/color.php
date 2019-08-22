<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/5/4
 * Time: 下午3:03
 */

use Toolkit\Cli\Color;

require dirname(__DIR__) . '/test/boot.php';

foreach (Color::getStyles() as $style) {
    printf("    %s: %s\n", $style, Color::apply($style, 'This is a message'));
}
