<?php

declare(strict_types=1);

use App\Domain\Enum\FlashTypes;
use App\Infrastructure\Handler;
use App\Infrastructure\Middleware\Template;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authentication\UserInterface;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Mezzio\MiddlewareFactory;
use Mezzio\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * FastRoute route configuration
 *
 * @see https://github.com/nikic/FastRoute
 *
 * Setup routes with a single request method:
 *
 * $app->get('/', App\Handler\HomePageHandler::class, 'home');
 * $app->post('/album', App\Handler\AlbumCreateHandler::class, 'album.create');
 * $app->put('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.put');
 * $app->patch('/album/{id:\d+}', App\Handler\AlbumUpdateHandler::class, 'album.patch');
 * $app->delete('/album/{id:\d+}', App\Handler\AlbumDeleteHandler::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', App\Handler\ContactHandler::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     App\Handler\ContactHandler::class,
 *     Mezzio\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->get('/', [
        AuthenticationMiddleware::class,
        Template\Extension\Authentication::class,
        Handler\Index::class,
    ], 'home');

    $app->get('/login', [
        Template\Extension\Authentication::class,
        Handler\Login::class,
    ], 'login');
    $app->post('/login', [
        AuthenticationMiddleware::class,
        static function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

            $flash = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
            assert($flash instanceof FlashMessagesInterface);


            $flash->flash(FlashTypes::INFO->value, [
                'title' => _('Login'),
                'body' => _('Successfully logged in!'),
            ]);

            return new RedirectResponse('/');
        }
    ], 'authenticate');

    $app->post('/logout', [
        AuthenticationMiddleware::class,
        static function(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
            $session = $request->getAttribute('session');
            assert($session instanceof SessionInterface);

            if ($session->has(UserInterface::class)) {
                $session->clear();
            }
            return new RedirectResponse('/');
        },
    ], 'logout');
};
