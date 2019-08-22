<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/5/4
 * Time: 下午3:13
 */

use Toolkit\Cli\Cli;
use Toolkit\Cli\Highlighter;

require dirname(__DIR__) . '/test/boot.php';

echo "Highlight current file content:\n";

// this is an comment
$rendered = Highlighter::create()->highlight(file_get_contents(__FILE__));

Cli::write($rendered);
