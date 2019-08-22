# CLI utils

[![License](https://img.shields.io/packagist/l/toolkit/cli-utils.svg?style=flat-square)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/toolkit/cli-utils)
[![Latest Stable Version](http://img.shields.io/packagist/v/toolkit/cli-utils.svg)](https://packagist.org/packages/toolkit/cli-utils)

Some useful utils for the php CLI env.

## Install

```bash
composer require toolkit/cli-utils
```

## Parse cli arguments and options

```php
use Toolkit\Cli\Flags;

$argv = $_SERVER['argv'];
// notice: must shift first element.
$script = \array_shift($argv);
// do parse
list($args, $shortOpts, $longOpts) = Flags::parseArgv($argv);
```

## PHP file highlight

```php
use Toolkit\Cli\Highlighter;

// this is an comment
$rendered = Highlighter::create()->highlight(file_get_contents(__FILE__));

\Toolkit\Cli\Cli::write($rendered);
```

![colors](./example/cli-php-file-highlight.jpg)

## Console color

![colors](./example/all-color-style.jpg)

## Cli downloader

```php
use Toolkit\Cli\Download;

$url  = 'http://no2.php.net/distributions/php-7.2.5.tar.bz2';
$down = Download::file($url, '');

// $down->setShowType('bar');
$down->start();
```

### progress bar output:

```text
Connected...
Mime-type: text/html; charset=utf-8
Being redirected to: http://no2.php.net/distributions/php-7.2.5.tar.bz2
Connected...
FileSize: 14280 kb
Mime-type: application/octet-stream
[========================================>                                                           ] 40% (3076/7590 kb)
```

### progress text output:

```text
Download: http://no2.php.net/distributions/php-7.2.5.tar.bz2
Save As: /path/to/php-7.2.5.tar.bz2

Connected ...
Got the file size: 14280 kb
Found the mime-type: application/octet-stream
Made some progress, downloaded 641 kb so far
```

## License

MIT
