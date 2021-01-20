<?php

declare(strict_types=1);

namespace App\Exception;

use EventSauce\EventSourcing\AggregateRootId;
use UnexpectedValueException;

final class AggregateRootNotFound extends UnexpectedValueException
{
    public static function byId(AggregateRootId $id): self
    {
        return new self(sprintf('Could not find aggregate root by id "%s".', $id->toString()));
    }
}
