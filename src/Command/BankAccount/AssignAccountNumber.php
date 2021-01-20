<?php

declare(strict_types=1);

namespace App\Command\BankAccount;

use App\Entity\BankAccountId;

final class AssignAccountNumber
{
    /**
     * @var BankAccountId
     */
    private $id;

    public function __construct(BankAccountId $accountId)
    {
        $this->id = $accountId;
    }

    public function id(): BankAccountId
    {
        return $this->id;
    }
}
