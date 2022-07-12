<?php

declare(strict_types=1);

namespace App;

use App\Domain\Repository as RepositoryInterface;
use App\Factory\Cache\PdoAdapterFactory;
use App\Factory\Database\PdoFactory;
use App\Factory\Reponse\HtmlResponseRendererFactory;
use App\Factory\Reponse\TemplateRendererInterfaceFactory;
use App\Infrastructure\Command\FactoryGenerator;
use App\Infrastructure\Middleware\Template\Extension\Authentication;
use App\Infrastructure\Repository;
use App\Infrastructure\Response\HtmlResponseRenderer;
use App\Infrastructure\Response\ResponseRenderer;
use Laminas\Di\InjectorInterface;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\Session\PhpSession;
use Mezzio\Authentication\UserRepository\PdoDatabase;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Session\Cache\CacheSessionPersistence;
use Mezzio\Session\SessionPersistenceInterface;
use PDO;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\PdoAdapter;

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
    /**
     * @return mixed[]
     */
    public function __invoke(): array
    {
        return [
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
        ];
    }

    /**
     * @return mixed[]
     */
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
                RepositoryInterface\User::class => Repository\User::class,
                AuthenticationInterface::class => PhpSession::class,
                UserRepositoryInterface::class => PdoDatabase::class,
                SessionPersistenceInterface::class => CacheSessionPersistence::class,
                CacheItemPoolInterface::class => PdoAdapter::class,
            ],
            'factories' => array_replace($this->getGeneratedFactories(), [
                HtmlResponseRenderer::class => HtmlResponseRendererFactory::class,
                PDO::class => PdoFactory::class,
                PdoAdapter::class => PdoAdapterFactory::class,
                'html-renderer' => TemplateRendererInterfaceFactory::class,
            ]),
            'delegators' => [
                InjectorInterface::class => [
                    InjectorDecoratorFactory::class,
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function getTemplates(): array
    {
        $debugMode = getenv('APP_ENV') === 'development';

        return [
            'extension' => 'twig',
            'paths' => [
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
            'debug' => $debugMode,
            'cache_dir' => $debugMode ? false : 'data/cache/twig',
            'extensions' => [
                Authentication::class,
            ],
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getGeneratedFactories(): array
    {
        // The generated factories.php file is compatible with
        // laminas-servicemanager's factory configuration.
        // This avoids using the abstract AutowireFactory, which
        // improves performance a bit since we spare some lookups.

        /**
         * @psalm-suppress MissingFile
         */
        if (file_exists(__DIR__ . '/../AppAoT/factories.php')) {
            return include __DIR__ . '/../AppAoT/factories.php';
        }

        return [];
    }
}
