<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Command\Deposit;
use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DepositController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(string $id, int $amount): Response
    {
        $this->messageBus->dispatch(
            new Deposit(
                BankAccountId::fromString($id),
                Currency::createFromCents($amount)
            )
        );

        return new RedirectResponse($this->urlGenerator->generate('app_account_view', [
            'id' => $id,
        ]));
    }
}
