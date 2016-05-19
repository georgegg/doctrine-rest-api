<?php
return [
    'database' => [
        'entityPath' => [__DIR__.'/../Entity/'],
        'proxyPath' => null,
        'autoGenerateProxies' => true,
        'driver' => 'pdo_pgsql',
        'host' => 'localhost',
        'dbname' => 'databaseName',
        'user' => 'postgres',
        'password' => 'password',
    ],
];