<?php

declare(strict_types=1);

namespace App;

use App\AoT\GeneratedInjector;
use Laminas\Di\InjectorInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Psr\Container\ContainerInterface;

use function class_exists;

final class InjectorDecoratorFactory implements DelegatorFactoryInterface
{
    // phpcs:disable
    /**
     * @param array<mixed> $options
     */
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        ?array $options = null
    ) : InjectorInterface {
        $injector = $callback();
        if (class_exists(GeneratedInjector::class)) {
            return new GeneratedInjector($injector);
        }

        return $injector;
    }
    // phpcs:enable
}
