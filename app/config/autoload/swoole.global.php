<?php

declare(strict_types=1);

use Platformsh\ConfigReader\Config;

Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

$config = new Config();

$port = 9501;
if ($config->isValidPlatform()) {
    $port = getenv('PORT');
}

return [
    'mezzio-swoole' => [
        'enable_coroutine' => true,
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => (int) $port,
            'options' => [
                'worker_num'      => 4,          // The number of HTTP Server Workers
                'task_worker_num' => 4,          // The number of Task Workers
                'task_enable_coroutine' => true, // optional to turn on task coroutine support
            ],
        ],
    ],
];
