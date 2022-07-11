<?php

declare(strict_types=1);

use App\Factory\Logger\ApplicationLoggerFactory;
use App\Factory\Logger\Handler\StreamHandlerFactory;
use App\Factory\Logger\LoggerListenerFactory;
use App\Factory\Logger\LoggingErrorListenerDelegatorFactory;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;

$handlers = [];
if (getenv('APP_ENV') === 'development') {
    $handlers[] = StreamHandler::class;
    $handlers[] = ErrorLogHandler::class;
}

$level = match (getenv('APP_LOG_LEVEL')) {
    'debug' => Level::Debug,
    'info' => Level::Info,
    'notice' => Level::Notice,
    'warning' => Level::Warning,
    'error' => Level::Error,
    'critical' => Level::Critical,
    'alert' => Level::Alert,
    'emergency' => Level::Emergency,
    default => getenv('APP_ENV') === 'development' ? Level::Debug : Level::Info,
};

return [
    'dependencies' => [
        'delegators' => [
            ErrorHandler::class => [
                LoggingErrorListenerDelegatorFactory::class,
            ],
        ],
        'invokables' => [
            ErrorLogHandler::class => ErrorLogHandler::class,
            IntrospectionProcessor::class => IntrospectionProcessor::class,
            WebProcessor::class => WebProcessor::class,
            PsrLogMessageProcessor::class => PsrLogMessageProcessor::class,
            MemoryUsageProcessor::class => MemoryUsageProcessor::class,
            MemoryPeakUsageProcessor::class => MemoryPeakUsageProcessor::class,
        ],
        'factories' => [
            StreamHandler::class => StreamHandlerFactory::class,
            LoggerInterface::class => ApplicationLoggerFactory::class,
            'logger-listener' => LoggerListenerFactory::class,
        ],
    ],
    'exception-logger' => [
        'channel' => 'exceptions',
        'handlers' => $handlers,
        'processors' => [
            PsrLogMessageProcessor::class,
            WebProcessor::class,
            MemoryUsageProcessor::class,
            MemoryPeakUsageProcessor::class,
        ],
        'timezone' => 'Europe/Berlin',
    ],
    'application-logger' => [
        'channel' => 'application',
        'handlers' => $handlers,
        'timezone' => 'Europe/Berlin',
        'processors' => [
            PsrLogMessageProcessor::class,
            IntrospectionProcessor::class,
            WebProcessor::class,
        ],
    ],
    'handlers' => [
        SyslogHandler::class => [
            'ident' => 'app',
            'facility' => LOG_USER,
            'level' => $level,
            'bubble' => true,
            'logopts' => LOG_PID,
        ],
        StreamHandler::class => [
            'stream' => 'data/logging/app.log',
            'level' => $level,
            'bubble' => true,
            'filePermission' => 0644,
            'useLocking' => true,
        ],
    ],
];
