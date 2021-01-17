<?php

declare(strict_types=1);

namespace App\ValueObject;

use Aeviiq\ValueObject\AbstractInt;

final class Currency extends AbstractInt
{
    private function __construct(int $value)
    {
        parent::__construct($value);
    }

    public static function createFromEuros(float $amount): self
    {
        return new self((int) ($amount * 100));
    }

    public static function createFromCents(int $amount): self
    {
        return new self($amount);
    }

    public function inEuros(): float
    {
        return $this->value / 100;
    }

    public function inCents(): int
    {
        return $this->value;
    }

    public function add(Currency $currency): self
    {
        return self::createFromCents($this->inCents() + $currency->inCents());
    }

    public function subtract(Currency $currency): self
    {
        return self::createFromCents($this->inCents() - $currency->inCents());
    }

    public static function getConstraints(): array
    {
        return [];
    }
}
