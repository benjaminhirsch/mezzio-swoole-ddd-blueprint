<?php

declare(strict_types=1);

namespace App;

use App\Factory\Database\PdoFactory;
use App\Infrastructure\Command\FactoryGenerator;
use App\Infrastructure\Response\HtmlResponseRenderer;
use App\Infrastructure\Response\ResponseRenderer;
use Laminas\Di\InjectorInterface;
use PDO;

use function array_merge;
use function file_exists;

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
            'aliases' => [ResponseRenderer::class => HtmlResponseRenderer::class],
            'factories' => array_merge($this->getGeneratedFactories(), [
                PDO::class => PdoFactory::class,
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
        return [
            'extension' => 'twig',
            'paths' => [
                'app'    => ['templates/app'],
                'error'  => ['templates/error'],
                'layout' => ['templates/layout'],
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
