<?php

declare(strict_types=1);

namespace App\Event\BankAccount;

use App\Entity\BankAccountId;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class AccountWasCreated implements SerializablePayload
{
    /**
     * @var AggregateRootId
     */
    private $accountId;

    public function __construct(AggregateRootId $accountId)
    {
        $this->accountId = $accountId;
    }

    public function id(): AggregateRootId
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
