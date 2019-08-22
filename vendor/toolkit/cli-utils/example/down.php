<?php
/**
 * demo for cli download file.
 */

use Toolkit\Cli\App;
use Toolkit\Cli\Download;

require dirname(__DIR__) . '/test/boot.php';

$app  = new App();
$url  = 'http://no2.php.net/distributions/php-7.2.5.tar.bz2';
$down = Download::create($url);

$type = $app->getOpt('type', 'text');

if ($type === 'bar') {
    $down->setShowType($type);
}

$down->start();
