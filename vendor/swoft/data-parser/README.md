# data parser

simple data parser for php

driver

- json(by `json_encode`)
- php(by `serialize`)
- swoole(by extension `swoole_serialize`)
- msgpack(by extension `msgpack`)

## Install

- composer command

```bash
composer require swoft/data-parser
```

## Usage

```php
$parser = new SwooleParser();
// $parser = new JsonParser();
// $parser = new PhpParser();
// $parser = new MsgPackParser();

// encode
$encoded = $parser->encode($data);

// decode
$decoded = $parser->encode($encoded);
```

## Unit testing

```bash
phpunit 
```

## LICENSE

The Component is open-sourced software licensed under the [Apache license](LICENSE).

