<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccountInformation;
use App\Entity\BankAccountId;
use App\Exception\BankAccount\AccountInformationNotFound;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

final class DoctrineAccountInformationRepository implements AccountInformationRepository
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ObjectRepository<AccountInformation>
     */
    private $repository;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->repository = $objectManager->getRepository(AccountInformation::class);
    }

    public function byAccount(BankAccountId $accountId): AccountInformation
    {
        $information = $this->repository->find($accountId->toString());
        if (null === $information) {
            throw AccountInformationNotFound::byId($accountId);
        }

        return $information;
    }

    public function save(AccountInformation $transaction): void
    {
        $this->objectManager->persist($transaction);
        $this->objectManager->flush();
    }

    public function delete(AccountInformation $transaction): void
    {
        $this->objectManager->remove($transaction);
        $this->objectManager->flush();
    }
}
