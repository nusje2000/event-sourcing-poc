<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use EventSauce\EventSourcing\AggregateRootRepository;

final class DepositHandler
{
    /**
     * @param AggregateRootRepository<BankAccount> $repository
     */
    public function __construct(private AggregateRootRepository $repository) {}

    public function __invoke(Deposit $deposit): void
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($deposit->id());
        $account->deposit($deposit->amount());
        $this->repository->persist($account);
    }
}
