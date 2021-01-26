<?php

declare(strict_types=1);

namespace App\Exception;

use App\ValueObject\Currency;
use DomainException;

final class CurrencyChangeWasBlocked extends DomainException
{
    public static function create(Currency $amount): self
    {
        return new self(sprintf('Cannot withdraw %.02f euros.', $amount->inEuros()));
    }
}
