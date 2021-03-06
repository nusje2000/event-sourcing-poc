<?php

declare(strict_types=1);

namespace App\Command;

use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;

final class Deposit
{
    /**
     * @var BankAccountId
     */
    private $id;

    /**
     * @var Currency
     */
    private $amount;

    public function __construct(BankAccountId $id, Currency $amount)
    {
        $this->id = $id;
        $this->amount = $amount;
    }

    public function id(): BankAccountId
    {
        return $this->id;
    }

    public function amount(): Currency
    {
        return $this->amount;
    }
}
