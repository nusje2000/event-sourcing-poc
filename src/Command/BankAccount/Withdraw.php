<?php

declare(strict_types=1);

namespace App\Command\BankAccount;

use App\Entity\BankAccountId;
use App\ValueObject\Currency;

final class Withdraw
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
