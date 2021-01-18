<?php

declare(strict_types=1);

namespace App\Event\BankAccount;

use App\ValueObject\Currency;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class CurrencyWasDeposited implements SerializablePayload
{
    /**
     * @var Currency
     */
    private $amount;

    public function __construct(Currency $amount)
    {
        $this->amount = $amount;
    }

    public function amount(): Currency
    {
        return $this->amount;
    }

    /**
     * @psalm-return array{amount: int}
     */
    public function toPayload(): array
    {
        return [
            'amount' => $this->amount->inCents(),
        ];
    }

    /**
     * @psalm-param array{amount: int} $payload
     */
    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(Currency::createFromCents($payload['amount']));
    }
}
