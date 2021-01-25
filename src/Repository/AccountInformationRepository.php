<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccountInformation;
use App\Entity\BankAccountId;

interface AccountInformationRepository
{
    public function byAccount(BankAccountId $accountId): AccountInformation;

    public function save(AccountInformation $transaction): void;

    public function delete(AccountInformation $transaction): void;
}
