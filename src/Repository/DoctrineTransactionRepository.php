<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BankAccountId;
use App\Entity\Transaction;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

final class DoctrineTransactionRepository implements TransactionRepository
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ObjectRepository<Transaction>
     */
    private $repository;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->repository = $objectManager->getRepository(Transaction::class);
    }

    /**
     * @return array<Transaction>
     */
    public function byAccount(BankAccountId $accountId): array
    {
        return $this->repository->findBy([
            'accountId' => $accountId->toString(),
        ], ['timestamp' => 'DESC']);
    }

    public function save(Transaction $transaction): void
    {
        $this->objectManager->persist($transaction);
        $this->objectManager->flush();
    }

    public function delete(Transaction $transaction): void
    {
        $this->objectManager->remove($transaction);
        $this->objectManager->flush();
    }
}
