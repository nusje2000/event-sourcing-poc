<?php

declare(strict_types=1);

namespace App\Command\BankAccount;

use App\Entity\BankAccount;
use EventSauce\EventSourcing\AggregateRootRepository;

final class WithdrawHandler
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

    public function handle(Withdraw $withdraw): void
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($withdraw->id());
        $account->withdraw($withdraw->amount());
        $this->repository->persist($account);
    }
}
