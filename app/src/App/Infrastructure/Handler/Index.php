<?php

declare(strict_types=1);

namespace App\Infrastructure\Handler;

use App\Infrastructure\Response\ResponseRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine\PostgreSQL;

use function go;

final class Index implements RequestHandlerInterface
{
    public function __construct(
        private readonly ResponseRenderer $renderer,
        private readonly PostgreSQL $PDO,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = null;
        go(function () use (&$response): void {
            $this->PDO->prepare('my_query', 'select * from items');

            // Bind the array data to the placeholders in the query
            $res = $this->PDO->execute('my_query', []);

            $response = $this->PDO->fetchAll($res);

          //  $response = $this->renderer->render('app::index', $request, ['foo' => $erg]);
        });

//        $this->PDO->prepare('wtf', 'select * from items');
//        $res = $this->PDO->execute('wtf', []);
//        //var_dump($this->PDO->fetchAll($res));

        return $this->renderer->render('app::index', $request, ['foo' => $response]);
    }
}
