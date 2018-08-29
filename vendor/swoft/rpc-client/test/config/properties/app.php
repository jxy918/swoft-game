<?php
return [
    "version"           => '1.0',
    'autoInitBean'      => true,
    'beanScan'          => [
        'Swoft\\Rpc\\Client\\Testing' => BASE_PATH."/Testing"
    ],
    'I18n'              => [
        'sourceLanguage' => '@root/resources/messages/',
    ],
    'env'               => 'Base',
    'user.stelin.steln' => 'fafafa',
    'Service'           => [
        'user' => [
            'timeout' => 3000
        ]
    ],
    'cache' => require dirname(__FILE__) . DS . "cache.php",
];
