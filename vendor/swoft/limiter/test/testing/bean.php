<?php

use Swoft\Redis\RedisDb;

return [
    'redis' => [
        'class'    => RedisDb::class,
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'database' => 0,
    ],
];