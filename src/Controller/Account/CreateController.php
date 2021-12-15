<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Command\Create;
use App\ValueObject\BankAccountId;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CreateController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function __invoke(): Response
    {
        $id = BankAccountId::generate();
        $this->messageBus->dispatch(Create::withId($id));

        return new RedirectResponse($this->urlGenerator->generate('app_account_view', [
            'id' => $id->toString(),
        ]));
    }
}
