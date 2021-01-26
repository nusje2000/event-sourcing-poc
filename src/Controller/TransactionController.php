<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TransactionRepository;
use App\ValueObject\BankAccountId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class TransactionController extends AbstractController
{
    /**
     * @var TransactionRepository
     */
    private $repository;

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/api/transactions/{accountId}")
     */
    public function transactions(string $accountId): JsonResponse
    {
        $transactions = $this->repository->byAccount(BankAccountId::fromString($accountId));

        return $this->json($transactions);
    }
}
