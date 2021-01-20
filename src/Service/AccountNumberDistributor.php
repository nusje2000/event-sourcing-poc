<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\AccountNumber;

interface AccountNumberDistributor
{
    public function fetch(): AccountNumber;
}
