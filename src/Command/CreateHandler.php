<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use EventSauce\EventSourcing\AggregateRootRepository;

final class CreateHandler
{
    /**
     * @param AggregateRootRepository<BankAccount> $repository
     */
    public function __construct(private AggregateRootRepository $repository) {}

    public function __invoke(Create $create): void
    {
        $account = BankAccount::initiate($create->id());
        $this->repository->persist($account);
    }
}
