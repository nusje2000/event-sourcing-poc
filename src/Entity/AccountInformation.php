<?php

declare(strict_types=1);

namespace App\Entity;

use App\Exception\MissingAccountNumber;
use App\ValueObject\AccountNumber;
use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
final class AccountInformation
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $accountId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $accountNumber;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $balance;

    public function __construct(BankAccountId $accountId, AccountNumber $accountNumber, Currency $balance)
    {
        $this->accountId = $accountId->toString();
        $this->accountNumber = $accountNumber->get();
        $this->balance = $balance->inCents();
    }

    public function accountId(): BankAccountId
    {
        return BankAccountId::fromString($this->accountId);
    }

    public function accountNumber(): AccountNumber
    {
        return AccountNumber::fromString($this->accountNumber);
    }

    public function balance(): Currency
    {
        return Currency::createFromCents($this->balance);
    }

    public function setBalance(Currency $balance): void
    {
        $this->balance = $balance->inCents();
    }
}
