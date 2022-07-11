<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Laminas\Di\CodeGenerator\InjectorGenerator;
use Laminas\Di\ConfigInterface;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\DependencyResolver;
use Psr\Container\ContainerInterface;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_map;

final class FactoryGenerator extends Command
{
    public function __construct(private ContainerInterface $container)
    {
        parent::__construct();
    }

    public function getDescription(): string
    {
        return 'Generate all factories';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = [
            __DIR__ . '/../../../../src/App/Domain',
            __DIR__ . '/../../../../src/App/Infrastructure',
        ];

        $diConfig = $this->container->get(ConfigInterface::class);

        $resolver = new DependencyResolver(new RuntimeDefinition(), $diConfig);
        $resolver->setContainer($this->container);

        $generator = new InjectorGenerator($diConfig, $resolver, 'App\AoT');
        $generator->setOutputDirectory(__DIR__ . '/../../../../src/AppAoT');

        $astLocator               = (new BetterReflection())->astLocator();
        $directoriesSourceLocator = new DirectoriesSourceLocator($directories, $astLocator);
        $reflector                = new DefaultReflector($directoriesSourceLocator);

        $generator->generate(array_map(static fn ($reflection) => $reflection->getName(), $reflector->reflectAllClasses()));

        return Command::SUCCESS;
    }
}
