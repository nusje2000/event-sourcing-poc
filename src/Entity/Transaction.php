<?php

declare(strict_types=1);

namespace App\Entity;

use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="account_transactions")
 */
final class Transaction implements JsonSerializable
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $accountId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $success;

    /**
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $timestamp;

    public function __construct(UuidInterface $id, BankAccountId $accountId, Currency $amount, bool $success, DateTimeInterface $timestamp)
    {
        $this->id = $id->toString();
        $this->accountId = $accountId->toString();
        $this->amount = $amount->inCents();
        $this->success = $success;
        $this->timestamp = $timestamp;
    }

    public function id(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    public function accountId(): BankAccountId
    {
        return BankAccountId::fromString($this->accountId);
    }

    public function amount(): Currency
    {
        return Currency::createFromCents($this->amount);
    }

    public function successfull(): bool
    {
        return $this->success;
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
