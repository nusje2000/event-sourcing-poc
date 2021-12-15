<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Command\Withdraw;
use App\Exception\CurrencyChangeWasBlocked;
use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class WithdrawController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function __invoke(Request $request, string $id, int $amount): Response
    {
        try {
            $this->messageBus->dispatch(
                new Withdraw(
                    BankAccountId::fromString($id),
                    Currency::createFromCents($amount)
                )
            );
        } catch (CurrencyChangeWasBlocked $exception) {
            $session = $request->getSession();
            assert($session instanceof Session);
            $session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('app_account_view', [
                'id' => $id,
            ])
        );
    }
}
