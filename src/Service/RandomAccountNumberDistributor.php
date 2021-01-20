<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\AccountNumber;

final class RandomAccountNumberDistributor implements AccountNumberDistributor
{
    public function fetch(): AccountNumber
    {
        return AccountNumber::fromString(sprintf('SA00AUSE%010d', random_int(0, 10 ** 10)));
    }
}
