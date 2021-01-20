<?php

declare(strict_types=1);

namespace App\ValueObject;

use Aeviiq\ValueObject\AbstractString;
use Symfony\Component\Validator\Constraints\Regex;

final class AccountNumber extends AbstractString
{
    private function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function fromString(string $accountNumber): self
    {
        return new self($accountNumber);
    }

    public static function getConstraints(): array
    {
        return [
            new Regex([
                'pattern' => '/[A-Z]{2}[0-9]{2}[A-Z]{4}[0-9]{10}/',
            ]),
        ];
    }

    protected function normalize($value): string
    {
        return strtoupper($value);
    }
}
