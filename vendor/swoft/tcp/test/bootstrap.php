<?php
use Composer\Autoload\ClassLoader;
use SwoftTest\Testing\TestApplication;

// current component dir
$componentDir  = dirname(__DIR__, 3);
$componentJson = $componentDir . '/composer.json';

// vendor at component dir
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
} elseif (file_exists(dirname(__DIR__, 3) . '/vendor/autoload.php')) {
    /** @var ClassLoader $loader */
    $loader = require dirname(__DIR__, 3) . '/vendor/autoload.php';

    // need load testing psr4 config map
    $composerData  = json_decode(file_get_contents($componentJson), true);
    foreach ($composerData['autoload-dev']['psr-4'] as $prefix => $dir) {
        $loader->addPsr4($prefix, $componentDir . '/' . $dir);
    }

    // application's vendor
} elseif (file_exists(dirname(__DIR__, 5) . '/autoload.php')) {
    /** @var ClassLoader $loader */
    $loader = require dirname(__DIR__, 5) . '/autoload.php';

    // need load testing psr4 config map
    $composerData  = json_decode(file_get_contents($componentJson), true);

    foreach ($composerData['autoload-dev']['psr-4'] as $prefix => $dir) {
        $loader->addPsr4($prefix, $componentDir . '/' . $dir);
    }
} else {
    exit('Please run "composer install" to install the dependencies' . PHP_EOL);
}

$application = new TestApplication([
    'basePath'        => __DIR__
]);
$application->setBeanFile(__DIR__ . '/testing/bean.php');
$application->run();
