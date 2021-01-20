<?php

declare(strict_types=1);

namespace App\Command\BankAccount;

use App\Entity\BankAccount;
use App\Entity\BankAccountId;
use App\Exception\BankAccount\AccountNumberIsAlreadyAssigned;
use App\Service\AccountNumberDistributor;
use EventSauce\EventSourcing\AggregateRootRepository;

final class AssignAccountNumberHandler
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    /**
     * @var AccountNumberDistributor
     */
    private $distributor;

    public function __construct(AggregateRootRepository $repository, AccountNumberDistributor $distributor)
    {
        $this->repository = $repository;
        $this->distributor = $distributor;
    }

    public function handle(AssignAccountNumber $assignment): void
    {
        $account = $this->fetchBankAccount($assignment->id());
        $this->assignAccountNumber($account);
        $this->repository->persist($account);
    }

    private function fetchBankAccount(BankAccountId $accountId): BankAccount
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve($accountId);

        return $account;
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
