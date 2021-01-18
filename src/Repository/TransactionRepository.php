<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BankAccountId;
use App\Entity\Transaction;

interface TransactionRepository
{
    /**
     * @return array<Transaction>
     */
    public function byAccount(BankAccountId $accountId): array;

    public function save(Transaction $transaction): void;

    public function delete(Transaction $transaction): void;
}
