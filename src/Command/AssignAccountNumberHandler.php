<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use App\Exception\AccountNumberIsAlreadyAssigned;
use App\Service\AccountNumberDistributor;
use EventSauce\EventSourcing\AggregateRootRepository;

final class AssignAccountNumberHandler
{
    /**
     * @param AggregateRootRepository<BankAccount> $repository
     */
    public function __construct(
        private AggregateRootRepository $repository,
        private AccountNumberDistributor $distributor
    ) {}

    public function __invoke(AssignAccountNumber $assignment): void
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($assignment->id());
        $this->assignAccountNumber($account);
        $this->repository->persist($account);
    }

    private function assignAccountNumber(BankAccount $account): void
    {
        if (null !== $account->accountNumber()) {
            throw AccountNumberIsAlreadyAssigned::create($account->id());
        }

        $number = $this->distributor->fetch();
        $account->assignAccountNumber($number);
    }
}
