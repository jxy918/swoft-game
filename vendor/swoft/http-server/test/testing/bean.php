<?php
return [
    'config' => [
        'path' => __DIR__ . '/../config',
    ],

    'httpDispatcher'    => [
        'afterMiddlewares' => [
            \Swoft\Http\Server\Middleware\ValidatorMiddleware::class
        ]
    ],
];
