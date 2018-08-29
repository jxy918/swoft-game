<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
return [
    'test' => [
        'name' => 'test',
        'uri' => [
            '127.0.0.1:6379',
            '127.0.0.1:6378',
        ],
        'maxIdel' => 1,
        'maxActive' => 1,
        'maxWait' => 1,
        'timeout' => 1,
        'balancer' => 'b',
        'useProvider' => true,
        'provider' => 'p',
    ],
    'test2' => [
        'name' => 'test2',
        'uri' => [
            '127.0.0.1:6379',
            '127.0.0.1:6378',
        ],
        'maxIdel' => 2,
        'maxActive' => 2,
        'maxWait' => 2,
        'timeout' => 2,
        'balancer' => 'b2',
        'useProvider' => true,
        'provider' => 'p2',
    ],
];
