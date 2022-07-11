<?php

declare(strict_types=1);

namespace App\Factory\Logger\Handler;

use App\Domain\Exception\MissingConfiguration;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

final class StreamHandlerFactory
{
    public function __invoke(ContainerInterface $container): StreamHandler
    {
        $handler = $container->get('config')['handlers'][StreamHandler::class] ?? null;

        if ($handler === null) {
            throw new MissingConfiguration(
                'StreamHandler values are missing in config'
            );
        }

        if (! isset($handler['stream'])) {
            throw new MissingConfiguration(
                'Missing `stream` in the handler configuration'
            );
        }

        if (! isset($handler['level'])) {
            throw new MissingConfiguration(
                'Missing `level` in the handler configuration'
            );
        }

        if (! isset($handler['bubble'])) {
            throw new MissingConfiguration(
                'Missing `bubble` in the handler configuration'
            );
        }

        if (! isset($handler['filePermission'])) {
            throw new MissingConfiguration(
                'Missing `filePermission` in the handler configuration'
            );
        }

        if (! isset($handler['useLocking'])) {
            throw new MissingConfiguration(
                'Missing `useLocking` in the handler configuration'
            );
        }

        return new StreamHandler(
            $handler['stream'],
            $handler['level'],
            $handler['bubble'],
            $handler['filePermission'],
            $handler['useLocking']
        );
    }
}
