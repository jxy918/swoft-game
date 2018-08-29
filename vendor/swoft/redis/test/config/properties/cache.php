<?php
return [
    'redis'     => [
        'name'        => 'redis',
        'uri'         => [
            'tcp://127.0.0.1:6379',
            'tcp://127.0.0.1:6379',
        ],
        'minActive'   => 8,
        'maxActive'   => 8,
        'maxWait'     => 8,
        'maxWaitTime' => 3,
        'maxIdleTime' => 60,
        'timeout'     => 8,
        'db'          => 2,
        'prefix'      => 'redis_',
        'serialize'   => 1,
    ],
    'demoRedis' => [
        'db'     => 2,
        'prefix' => 'demo_redis_',
    ],
];
