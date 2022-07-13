<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\User;

use App\Application\Event\UserCreated;
use App\Domain\Entity\User;
use App\Infrastructure\InputFilter\User\Registration;
use DateTimeImmutable;
use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use Mezzio\Helper\UrlHelper;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

use function assert;
use function is_array;
use function password_hash;

use const PASSWORD_ARGON2ID;

final class Register implements MiddlewareInterface
{
    public function __construct(
        private readonly \App\Application\Repository\User $userRepository,
        private readonly UrlHelper $urlHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InputFilterPluginManager $pluginManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();
        assert(is_array($body));

        $inputFilter = $this->pluginManager->get(Registration::class);
        assert($inputFilter instanceof InputFilterInterface);

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            if ($inputFilter->setData($body)->isValid()) {
                $user = new User(
                    Uuid::uuid4(),
                    $body['identity'],
                    password_hash($body['password'], PASSWORD_ARGON2ID),
                    null,
                    new DateTimeImmutable(),
                    new DateTimeImmutable(),
                );

                $this->userRepository->create($user);
                $this->eventDispatcher->dispatch(UserCreated::create($user));

                return new RedirectResponse($this->urlHelper->generate('login'));
            }
        }

        return $handler->handle($request->withAttribute(InputFilterInterface::class, $inputFilter));
    }
}
