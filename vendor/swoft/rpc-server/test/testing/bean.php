<?php

use Swoft\Rpc\Server\ServiceDispatcher;
use SwoftTest\Rpc\Server\Testing\Middleware\UserMd;

return [
    'config'            => [
        'path' => __DIR__ . '/../config',
    ],
    'serviceDispatcher' => [
        'class'       => ServiceDispatcher::class,
        'middlewares' => [
            UserMd::class
        ]
    ]
];