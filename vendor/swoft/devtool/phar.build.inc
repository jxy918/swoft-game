<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * - the is a config file for compile phar package.
 * User: Inhere
 * Date: 2018/1/26 0026
 * Time: 22:11
 * @var \Swoft\Devtool\PharCompiler $compiler
 */

// config
$compiler
  ->stripComments(true)
  ->setShebang(true)
  ->addExclude([
    'test',
  ])
  ->addFile([
    'LICENSE',
    'composer.json',
    'README.md',
    'tests/bootstrap.php',
  ])
  ->setCliIndex('bin/devtool')
  // ->setWebIndex('web/index.php')
  // ->setVersionFile('config/config.php')
;

// Console 下的 Command Controller 命令类不去除注释，注释上是命令帮助信息
$compiler->setStripFilter(function ($file) {
  /** @var \SplFileInfo $file */
  $path = $file->getPath();

  if (strpos($path, 'swoft')) {
    return false;
  }

  return false === strpos($file->getFilename(), 'Command.php');
});
