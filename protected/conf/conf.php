<?php
return [
    'db' => [
        'dsn' => 'mysql:host=192.168.1.35;dbname=qinwuwx',
        'username' => 'root',
        'password' => '123456',
    ],
    'params' => [

    ],
    'env' => [
        'debug' => true,
        'is_test' => true,
        'api' => 'api',
        'web' => 'admin',
        'cmd' => 'cmd',
        'sign' => [
            'timesign' => 600,
            'sign_secretkey' => 'UJG9027AW6d7yfMmD',
        ]
    ],
];