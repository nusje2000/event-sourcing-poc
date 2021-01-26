<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BankAccount;
use EventSauce\EventSourcing\AggregateRootRepository;

final class CreateHandler
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

    public function handle(Create $create): void
    {
        $account = BankAccount::initiate($create->id());
        $this->repository->persist($account);
    }
}
