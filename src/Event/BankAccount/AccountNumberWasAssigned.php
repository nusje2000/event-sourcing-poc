<?php

declare(strict_types=1);

namespace App\Event\BankAccount;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class AccountNumberWasAssigned implements SerializablePayload
{
    private string $accountNumber;

    public function __construct(string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function accountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @psalm-return array{account_number: string}
     */
    public function toPayload(): array
    {
        return [
            'account_number' => $this->accountNumber,
        ];
    }

    /**
     * @psalm-param array{account_number: string} $payload
     */
    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self($payload['account_number']);
    }
}
