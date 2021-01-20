<?php

declare(strict_types=1);

namespace App\Exception\BankAccount;

use App\Entity\BankAccountId;
use LogicException;

final class AccountNumberIsAlreadyAssigned extends LogicException
{
    public static function create(BankAccountId $id): self
    {
        return new self(sprintf(
            'Attempted to assign an account number, but there is already an account number present on account with id "%s".',
            $id->toString()
        ));
    }
}
