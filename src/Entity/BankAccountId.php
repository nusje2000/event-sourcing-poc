<?php

declare(strict_types=1);

namespace App\Entity;

use Aeviiq\ValueObject\AbstractString;
use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;

final class BankAccountId extends AbstractString implements AggregateRootId
{
    private function __construct(string $id)
    {
        parent::__construct($id);
    }

    public function toString(): string
    {
        return $this->get();
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

    public static function getConstraints(): array
    {
        return [
            new NotBlank(),
        ];
    }
}
