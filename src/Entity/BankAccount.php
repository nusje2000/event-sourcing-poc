<?php

declare(strict_types=1);

namespace App\Entity;

use App\Event\AccountNumberWasAssigned;
use App\Event\AccountWasCreated;
use App\Event\CurrencyChangeWasBlocked;
use App\Event\CurrencyWasChanged;
use App\Event\CurrencyWasDeposited;
use App\Event\CurrencyWasWithdawn;
use App\ValueObject\AccountNumber;
use App\ValueObject\BankAccountId;
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

    /**
     * @var int
     */
    private $blockAttempts = 0;

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
        return $this->aggregateRootId();
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

    public function withdraw(Currency $currency): bool
    {
        if ($this->currency->subtract($currency)->isLessThan($this->maximumDebt())) {
            $this->recordThat(new CurrencyChangeWasBlocked($currency->multiply(-1)));

            return false;
        }

        $this->recordThat(new CurrencyWasChanged($currency->multiply(-1)));

        return true;
    }

    public function deposit(Currency $currency): bool
    {
        $this->recordThat(new CurrencyWasChanged($currency));

        return true;
    }

    public function applyAccountWasCreated(AccountWasCreated $account): void
    {
        $this->aggregateRootId = $account->id();
    }

    public function applyAccountNumberWasAssigned(AccountNumberWasAssigned $assignment): void
    {
        $this->accountNumber = $assignment->accountNumber();
    }

    public function applyCurrencyWasChanged(CurrencyWasChanged $change): void
    {
        $this->currency = $this->currency->add($change->amount());
    }

    public function applyCurrencyChangeWasBlocked(): void
    {
        ++$this->blockAttempts;
    }

    private function maximumDebt(): Currency
    {
        return Currency::createFromEuros(-100.0);
    }
}
