<?php

declare(strict_types=1);

namespace App\Command\BankAccount;

use App\Entity\BankAccount;
use App\Entity\BankAccountId;
use EventSauce\EventSourcing\AggregateRootRepository;

final class DepositHandler
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    public function __construct(AggregateRootRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Deposit $deposit): void
    {
        $account = $this->fetchBankAccount($deposit->id());
        $account->deposit($deposit->amount());
        $this->repository->persist($account);
    }

    private function fetchBankAccount(BankAccountId $accountId): BankAccount
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($accountId);

        return $account;
    }
}
