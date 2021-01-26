<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use EventSauce\EventSourcing\AggregateRootRepository;

final class DepositHandler
{
    /**
     * @var AggregateRootRepository<BankAccount>
     */
    private $repository;

    /**
     * @param AggregateRootRepository<BankAccount> $repository
     */
    public function __construct(AggregateRootRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(Deposit $deposit): void
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($deposit->id());
        $account->deposit($deposit->amount());
        $this->repository->persist($account);
    }
}
