<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\Template\Extension;

use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Flash\FlashMessagesInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function assert;

final class Flash extends AbstractExtension implements MiddlewareInterface
{
    /** @var callable */
    private $rendererFactory;
    private null|FlashMessagesInterface $flashMessages;

    public function __construct(callable $rendererFactory)
    {
        $this->rendererFactory = $rendererFactory;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('showFlashMessages', [$this, 'showFlashMessages']),
        ];
    }

    public function showFlashMessages(null|string $flashType = null): ?string
    {
        $renderer = ($this->rendererFactory)();

        return $renderer->render('app::flash-messages', [
            'type' => $flashType,
            'toasts' => $this->flashMessages?->getFlashes() ?? [],
        ]);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $flashAttribute = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
        assert($flashAttribute instanceof FlashMessagesInterface || $flashAttribute === null);
        $this->flashMessages = $flashAttribute;

        return $handler->handle($request);
    }
}
