<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\Currency;
use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

final class Transaction implements JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var BankAccountId
     */
    private $accountId;

    /**
     * @var Currency
     */
    private $amount;

    /**
     * @var DateTimeInterface
     */
    private $timestamp;

    public function __construct(UuidInterface $id, BankAccountId $accountId, Currency $amount, DateTimeInterface $timestamp)
    {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->amount = $amount;
        $this->timestamp = $timestamp;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function accountId(): BankAccountId
    {
        return $this->accountId;
    }

    public function amount(): Currency
    {
        return $this->amount;
    }

    public function timestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * @return array{id: string, account_id: string, amount: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->toString(),
            'account_id' => $this->accountId()->toString(),
            'amount' => $this->amount()->inCents(),
            'timestamp' => $this->timestamp()->format(DateTimeInterface::ATOM),
        ];
    }
}
