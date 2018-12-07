<?php

/*
 * This file is part of Swoft.
 * (c) Swoft <group@swoft.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'consul' => [
        'address' => '192.168.7.197',
        'port'    => 8500,
        'register' => [
            'id'                => 'notify',
            'name'              => 'notify',
            'tags'              => ['notify'],
            'enableTagOverride' => false,
            'service'           => [
                'address' => '192.168.7.197',
                'port'   => '20000',
            ],
            'check'             => [
                'id'       => 'notify',
                'name'     => 'notify',
                'tcp'      => '192.168.7.197:20000',
                'interval' => 10,
                'timeout'  => 2,
            ],
        ],
        'discovery' => [
            'name' => 'notify',
            'dc' => 'dc1',
            'near' => '',
            'tag' =>'',
            'passing' => true
        ]
    ],
];