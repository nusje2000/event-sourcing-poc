<?php

declare(strict_types=1);

namespace App\Entity;

use App\Event\BankAccount\AccountNumberWasAssigned;
use App\Event\BankAccount\AccountWasCreated;
use App\Event\BankAccount\CurrencyWasDeposited;
use App\Event\BankAccount\CurrencyWasWithdawn;
use App\ValueObject\AccountNumber;
use App\ValueObject\Currency;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

/**
 * @method BankAccountId aggregateRootId()
 */
final class BankAccount implements AggregateRoot
{
    use AggregateRootBehaviour;

    /**
     * @var AccountNumber|null
     */
    private $accountNumber;

    /**
     * @var Currency
     */
    private $currency;

    private function __construct(BankAccountId $aggregateRootId)
    {
        $this->aggregateRootId = $aggregateRootId;
        $this->currency = Currency::createFromCents(0);
    }

    public static function initiate(BankAccountId $accountId): self
    {
        $account = new self($accountId);
        $account->recordThat(new AccountWasCreated($accountId));

        return $account;
    }

    public function id(): BankAccountId
    {
        return $this->aggregateRootId;
    }

    public function accountNumber(): ?AccountNumber
    {
        return $this->accountNumber;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function assignAccountNumber(AccountNumber $number): void
    {
        $this->recordThat(new AccountNumberWasAssigned($number));
    }

    public function withdraw(Currency $currency): void
    {
        $this->recordThat(new CurrencyWasWithdawn($currency));
    }

    public function deposit(Currency $currency): void
    {
        $this->recordThat(new CurrencyWasDeposited($currency));
    }

    public function applyAccountWasCreated(AccountWasCreated $account): void
    {
        $this->aggregateRootId = $account->id();
    }

    public function applyAccountNumberWasAssigned(AccountNumberWasAssigned $assignment): void
    {
        $this->accountNumber = $assignment->accountNumber();
    }

    public function applyCurrencyWasDeposited(CurrencyWasDeposited $deposit): void
    {
        $this->currency = $this->currency->add($deposit->amount());
    }

    public function applyCurrencyWasWithdawn(CurrencyWasWithdawn $withdraw): void
    {
        $this->currency = $this->currency->subtract($withdraw->amount());
    }
}
