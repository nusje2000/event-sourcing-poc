<?php

declare(strict_types=1);

namespace App\Event\BankAccount;

use App\Entity\BankAccountId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class AccountWasCreated implements SerializablePayload
{
    /**
     * @var BankAccountId
     */
    private $accountId;

    public function __construct(BankAccountId $accountId)
    {
        $this->accountId = $accountId;
    }

    public function id(): BankAccountId
    {
        return $this->accountId;
    }

    /**
     * @psalm-return array{id: string}
     */
    public function toPayload(): array
    {
        return [
            'id' => $this->accountId->toString(),
        ];
    }

    /**
     * @psalm-param array{id: string} $payload
     */
    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(BankAccountId::fromString($payload['id']));
    }
}
