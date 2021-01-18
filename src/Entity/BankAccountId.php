<?php

declare(strict_types=1);

namespace App\Entity;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;

final class BankAccountId implements AggregateRootId
{
    /**
     * @var string
     */
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    /**
     * @return BankAccountId
     */
    public static function fromString(string $aggregateRootId): AggregateRootId
    {
        return new self($aggregateRootId);
    }

    public static function generate(): BankAccountId
    {
        return new self(Uuid::uuid4()->toString());
    }
}
