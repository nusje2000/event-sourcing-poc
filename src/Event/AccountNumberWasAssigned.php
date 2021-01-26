<?php

declare(strict_types=1);

namespace App\Event;

use App\ValueObject\AccountNumber;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class AccountNumberWasAssigned implements SerializablePayload
{
    /**
     * @var AccountNumber
     */
    private $accountNumber;

    public function __construct(AccountNumber $accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    public function accountNumber(): AccountNumber
    {
        return $this->accountNumber;
    }

    /**
     * @psalm-return array{account_number: string}
     */
    public function toPayload(): array
    {
        return [
            'account_number' => $this->accountNumber->get(),
        ];
    }

    /**
     * @psalm-param array{account_number: string} $payload
     */
    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(AccountNumber::fromString($payload['account_number']));
    }
}
