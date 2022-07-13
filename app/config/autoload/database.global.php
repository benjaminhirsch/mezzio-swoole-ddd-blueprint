<?php

declare(strict_types=1);

use Platformsh\ConfigReader\Config;

$config = new Config();

if ($config->isValidPlatform() && $config->inRuntime()) {
    if (! $config->hasRelationship('database')) {
        throw new RuntimeException('Missing database relationship in platform.sh config');
    }

    if (! $config->hasRelationship('redis')) {
        throw new RuntimeException('Missing redis relationship in platform.sh config');
    }

    $postgresCredentials = $config->credentials('database');
    $redisCredentials    = $config->credentials('redis');

    if (isset($redisCredentials['username'], $redisCredentials['password'])) {
        $redisContext = ['auth' => $redisCredentials['username'], $redisCredentials['password']];
    }
}

return [
    'database' => [
        'default' => [
            'adapter' => 'pgsql',
            'dbname' => $postgresCredentials['path'] ?? null,
            'host' => $postgresCredentials['host'] ?? null,
            'port' => $postgresCredentials['port'] ?? null,
            'user' => $postgresCredentials['username'] ?? null,
            'pass' => $postgresCredentials['password'] ?? null,
        ],
    ],
    'redis' => [
        'default' => [
            'host' => $redisCredentials['host'] ?? null,
            'port' => $redisCredentials['port'] ?? 6379,
            'timeout' => $redisCredentials['timeout'] ?? 0,
            'persistentId' => $redisCredentials['persistentId'] ?? null,
            'retryInterval' => $redisCredentials['retryInterval'] ?? 0,
            'readTimeout' => $redisCredentials['readTimeout'] ?? 0,
            'context' => $redisContext ?? [],
        ],
    ],
];
