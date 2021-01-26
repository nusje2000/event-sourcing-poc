<?php

declare(strict_types=1);

namespace App\Command;

use App\ValueObject\BankAccountId;

final class Create
{
    /**
     * @var BankAccountId
     */
    private $id;

    private function __construct(BankAccountId $id)
    {
        $this->id = $id;
    }

    public static function createWithGeneratedId(): self
    {
        return new self(BankAccountId::generate());
    }

    public static function withId(BankAccountId $id): self
    {
        return new self($id);
    }

    public function id(): BankAccountId
    {
        return $this->id;
    }
}
