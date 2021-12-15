<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use App\Exception\CurrencyChangeWasBlocked;
use EventSauce\EventSourcing\AggregateRootRepository;

final class WithdrawHandler
{
    /**
     * @param AggregateRootRepository<BankAccount> $repository
     */
    public function __construct(private AggregateRootRepository $repository) {}

    public function __invoke(Withdraw $withdraw): void
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($withdraw->id());
        $success = $account->withdraw($withdraw->amount());
        $this->repository->persist($account);

        if (false === $success) {
            throw CurrencyChangeWasBlocked::create($withdraw->amount());
        }
    }
}
