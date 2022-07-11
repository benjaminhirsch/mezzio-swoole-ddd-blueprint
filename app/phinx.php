<?php

declare(strict_types=1);

$container = require 'config/container.php';

$config = $container->get('config');

assert(isset(
    $config['database']['default']['adapter'],
    $config['database']['default']['dbname'],
    $config['database']['default']['host'],
    $config['database']['default']['port'],
    $config['database']['default']['user'],
    $config['database']['default']['pass'],
));

$dbSettings = $config['database']['default'];

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/data/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/data/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_environment' => 'development',
        'production' => [
            'adapter' => $dbSettings['adapter'],
            'host' => $dbSettings['host'],
            'name' => $dbSettings['dbname'],
            'user' => $dbSettings['user'],
            'pass' => $dbSettings['pass'],
            'port' => $dbSettings['port'],
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => $dbSettings['adapter'],
            'host' => $dbSettings['host'],
            'name' => $dbSettings['dbname'],
            'user' => $dbSettings['user'],
            'pass' => $dbSettings['pass'],
            'port' => $dbSettings['port'],
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => $dbSettings['adapter'],
            'host' => $dbSettings['host'],
            'name' => $dbSettings['dbname'],
            'user' => $dbSettings['user'],
            'pass' => $dbSettings['pass'],
            'port' => $dbSettings['port'],
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
