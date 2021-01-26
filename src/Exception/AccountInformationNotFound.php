<?php

declare(strict_types=1);

namespace App\Exception;

use App\ValueObject\BankAccountId;
use UnexpectedValueException;

final class AccountInformationNotFound extends UnexpectedValueException
{
    public static function byId(BankAccountId $accountId): self
    {
        return new self(sprintf('Could not find bank account information for id "%s".', $accountId->toString()));
    }
}
