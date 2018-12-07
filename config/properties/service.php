<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'dbproxy' => [
        'name'        => 'dbproxy',
        'uri'         => [
            '192.168.7.197:8099',
            '192.168.7.197:8099',
        ],
        'minActive'   => 8,
        'maxActive'   => 20,
        'maxWait'     => 8,
        'maxWaitTime' => 3,
        'maxIdleTime' => 60,
        'timeout'     => 20,
        'useProvider' => true,
        'balancer' => 'random',
        'provider' => 'consul',
    ],
    'notify' => [
        'name'        => 'notify',
        'uri'         => [
            '192.168.7.197:20000',
            '192.168.7.197:20000',
        ],
        'minActive'   => 8,
        'maxActive'   => 8,
        'maxWait'     => 8,
        'maxWaitTime' => 3,
        'maxIdleTime' => 60,
        'timeout'     => 20,
        'useProvider' => true,
        'balancer' => 'random',
        'provider' => 'consul',
    ],
];