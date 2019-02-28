<?php
return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),

    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=http_files_test;port=8889',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
    ],
];
