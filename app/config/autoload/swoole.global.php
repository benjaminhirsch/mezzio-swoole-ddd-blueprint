<?php

declare(strict_types=1);

Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

return [
    'mezzio-swoole' => [
        'enable_coroutine' => true,
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 9501,
            'options' => [
                'worker_num'      => 4,          // The number of HTTP Server Workers
                'task_worker_num' => 4,          // The number of Task Workers
                'task_enable_coroutine' => true, // optional to turn on task coroutine support
            ],
        ],
    ],
];
