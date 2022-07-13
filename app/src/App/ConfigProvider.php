<?php

declare(strict_types=1);

namespace App;

use App\Application\Event;
use App\Application\Listener;
use App\Application\Session\SessionStorage;
use App\Factory\Cache\RedisFactory;
use App\Factory\Cache\RedisSessionCacheAdapterFactory;
use App\Factory\Database\PdoFactory;
use App\Factory\Database\PostgreSQLFactory;
use App\Factory\Middleware\Template\Extension\FlashFactory;
use App\Factory\Reponse\HtmlResponseRendererFactory;
use App\Factory\Reponse\TemplateRendererInterfaceFactory;
use App\Factory\Repository\UserFactory;
use App\Infrastructure\Command\FactoryGenerator;
use App\Infrastructure\InputFilter;
use App\Infrastructure\Middleware\Template\Extension\Authentication;
use App\Infrastructure\Middleware\Template\Extension\Flash;
use App\Infrastructure\Repository;
use App\Infrastructure\Response\HtmlRenderer;
use App\Infrastructure\Response\HtmlResponseRenderer;
use App\Infrastructure\Response\ResponseRenderer;
use Laminas\Di\GeneratedInjectorDelegator;
use Laminas\Di\InjectorInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserRepository\PdoDatabase;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Session\Cache\CacheSessionPersistence;
use Mezzio\Session\SessionPersistenceInterface;
use Mezzio\Swoole\Event\TaskEvent;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mezzio\Swoole\Task\TaskInvokerListener;
use PDO;
use Psr\EventDispatcher\EventDispatcherInterface;
use Redis;
use Swoole\Coroutine\PostgreSQL;
use Twig\Loader\FilesystemLoader;

use function array_replace;
use function file_exists;
use function getenv;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /** @return mixed[] */
    public function __invoke(): array
    {
        return [
            'mezzio-swoole' => [
                'swoole-http-server' => [
                    'listeners' => [
                        Event\UserCreated::class => [
                            Listener\UserCreated::class,
                        ],
                        TaskEvent::class => [
                            TaskInvokerListener::class,
                        ],
                    ],
                ],
            ],
            'mezzio-session-cache' => [
                'cookie_name' => 'app-session',
                'cookie_domain' => null,
                'cookie_path' => '/',
                'cookie_secure' => false,
                'cookie_http_only' => false,
                'cookie_same_site' => 'Lax',
                'cache_limiter' => 'nocache',
                'cache_expire' => 60 * 60 * 24 * 7, // 7 days - only relevant, when persistent = true
                'last_modified' => null,
                'persistent' => false,
                'cache_item_pool_service' => SessionStorage::class,
            ],
            'authentication' => [
                'redirect' => '/login',
                'username' => 'identity',
                'password' => 'password',
                'pdo' => [
                    'table' => 'users',
                    'field' => [
                        'identity' => 'email',
                        'password' => 'password',
                    ],
                    'service' => PDO::class,
                ],
            ],
            'laminas-cli' => [
                'commands' => [
                    'app:factory:generate' => FactoryGenerator::class,
                ],
            ],
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'input_filters' => $this->getInputFilters(),
        ];
    }

    /** @return mixed[] */
    public function getDependencies(): array
    {
        return [
            'auto' => [
                'aot' => [
                    'namespace' =>  __NAMESPACE__ . '\\AoT',
                    'directory' => __DIR__ . '/../AppAoT',
                ],
            ],
            'aliases' => [
                ResponseRenderer::class => HtmlResponseRenderer::class,
                Application\Repository\User::class => Repository\User::class,
                AuthenticationInterface::class => PhpSession::class,
                UserRepositoryInterface::class => PdoDatabase::class,
                SessionPersistenceInterface::class => CacheSessionPersistence::class,
                EventDispatcherInterface::class => \Mezzio\Swoole\Event\EventDispatcherInterface::class,
            ],
            'factories' => array_replace($this->getGeneratedFactories(), [
                Repository\User::class => UserFactory::class,
                Redis::class => RedisFactory::class,
                HtmlResponseRenderer::class => HtmlResponseRendererFactory::class,
                PDO::class => PdoFactory::class,
                PostgreSQL::class => PostgreSQLFactory::class,
                SessionStorage::class => RedisSessionCacheAdapterFactory::class,
                HtmlRenderer::class => TemplateRendererInterfaceFactory::class,
                Flash::class => FlashFactory::class,
            ]),
            'delegators' => [
                Listener\UserCreated::class => [
                    DeferredServiceListenerDelegator::class,
                ],
                InjectorInterface::class => [
                    GeneratedInjectorDelegator::class,
                ],
            ],
        ];
    }

    /** @return mixed[] */
    public function getTemplates(): array
    {
        $debugMode = getenv('APP_ENV') === 'development';

        return [
            'extension' => 'twig',
            'paths' => [
                FilesystemLoader::MAIN_NAMESPACE    => ['templates/app'],
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
            'debug' => $debugMode,
            'cache_dir' => $debugMode ? false : 'data/cache/twig',
            'extensions' => [
                Authentication::class,
                Flash::class,
            ],
        ];
    }

    /** @return array<mixed> */
    private function getGeneratedFactories(): array
    {
        // The generated factories.php file is compatible with
        // laminas-servicemanager's factory configuration.
        // This avoids using the abstract AutowireFactory, which
        // improves performance a bit since we spare some lookups.

        /** @psalm-suppress MissingFile */
        if (file_exists(__DIR__ . '/../AppAoT/factories.php')) {
            return include __DIR__ . '/../AppAoT/factories.php';
        }

        return [];
    }

    /** @return array{'factories':array<string, string>} */
    private function getInputFilters(): array
    {
        return [
            'factories' => [
                InputFilter\User\Registration::class => InvokableFactory::class,
            ],
        ];
    }
}
