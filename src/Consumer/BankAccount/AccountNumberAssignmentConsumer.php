<?php

declare(strict_types=1);

namespace App\Consumer\BankAccount;

use App\Command\BankAccount\AssignAccountNumber;
use App\Entity\BankAccountId;
use App\Event\BankAccount\AccountWasCreated;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use League\Tactician\CommandBus;
use UnexpectedValueException;

final class AccountNumberAssignmentConsumer implements Consumer
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handle(Message $message): void
    {
        $event = $message->event();
        if ($event instanceof AccountWasCreated) {
            $this->commandBus->handle(new AssignAccountNumber($this->getBankAccountIdFromMessage($message)));
        }
    }

    private function getBankAccountIdFromMessage(Message $message): BankAccountId
    {
        $id = $message->aggregateRootId();
        if (!$id instanceof BankAccountId) {
            throw new UnexpectedValueException(sprintf('Expected aggregate root id to be an instance of "%s".', BankAccountId::class));
        }

        return $id;
    }
}
