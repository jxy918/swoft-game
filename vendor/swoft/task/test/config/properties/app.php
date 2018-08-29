<?php
return [
    "version"      => '1.0',
    'autoInitBean' => true,
    'beanScan'     => [
        'SwoftTest\\Task\\Tasks' => BASE_PATH . "/Cases/Tasks",
        'Swoft\\Tasks'            => BASE_PATH . "/../src",
    ],
];
