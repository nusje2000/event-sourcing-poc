<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Repository\AccountInformationRepository;
use App\Repository\TransactionRepository;
use App\ValueObject\BankAccountId;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ViewController
{
    public function __construct(
        private Environment $twig,
        private AccountInformationRepository $informationRepository,
        private TransactionRepository $transactionRepository
    ) {}

    public function __invoke(string $id): Response
    {
        $accountId = BankAccountId::fromString($id);

        return new Response($this->twig->render('overview.html.twig', [
            'id' => $accountId,
            'account' => $this->informationRepository->byAccount($accountId),
            'transactions' => $this->transactionRepository->byAccount($accountId),
        ]));
    }
}
