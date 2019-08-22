<?php

use Swoft\Db\Database;
use Swoft\Db\Pool;
use SwoftTest\Db\Testing\DbSelector;

return [
    'config'   => [
        'path' => __DIR__ . '/../config',
    ],
    'db'       => [
        'class'    => Database::class,
        'dsn'      => 'mysql:dbname=test;host=127.0.0.1',
        'username' => 'root',
        'password' => 'swoft123456',
        'charset'  => 'utf8mb4',
        // 'prefix'   => 't_',
        'options'  => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
        ],
        'config'   => [
            'collation' => 'utf8mb4_unicode_ci',
            'strict'    => false,
            'timezone'  => '+8:00',
            'modes'     => 'NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES',
            'engine'    => 'innodb'
            //'fetchMode' => PDO::FETCH_ASSOC,
        ],
    ],
    'db2'      => [
        'charset' => 'utf8mb4',
        'class'   => Database::class,
        'writes'  => [
            [
                'dsn'      => 'mysql:dbname=test;host=127.0.0.1',
                'username' => 'root',
                'password' => 'swoft123456',
            ],
        ],
        'reads'   => [
            [
                'dsn'      => 'mysql:dbname=test;host=127.0.0.1',
                'username' => 'root',
                'password' => 'swoft123456',
            ]
        ],
    ],
    'db.pool2' => [
        'class'    => Pool::class,
        'database' => bean('db2')
    ],
    'db3'      => [
        'class'      => Database::class,
        'dsn'        => 'mysql:dbname=test2;host=127.0.0.1',
        'username'   => 'root',
        'password'   => 'swoft123456',
        'dbSelector' => bean(DbSelector::class)
    ],
    'db.pool3' => [
        'class'    => Pool::class,
        'database' => bean('db3')
    ],
    'db4'      => [
        'class'    => Database::class,
        'dsn'      => 'mysql:dbname=test2;host=127.0.0.1',
        'username' => 'root',
        'password' => 'swoft123456',
    ],
    'db.pool4' => [
        'class'    => Pool::class,
        'database' => bean('db4')
    ],
];

