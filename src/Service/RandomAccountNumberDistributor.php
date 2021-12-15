<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\AccountNumber;
use Psr\Log\LoggerInterface;

final class RandomAccountNumberDistributor implements AccountNumberDistributor
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function fetch(): AccountNumber
    {
        $this->logger->notice('Beep Boop, accessing some slow external service...');
        sleep(1);
        $this->logger->notice('Performing calculations...');
        sleep(1);
        $this->logger->warning('DANGER TO MANIFOLD');
        sleep(1);
        $this->logger->notice('Wow, wasn\'t that fast, it only took ~3 seconds :)');

        return AccountNumber::fromString(sprintf('SA00AUSE%010d', random_int(0, 10 ** 10)));
    }
}
