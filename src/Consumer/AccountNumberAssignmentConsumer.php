<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Command\AssignAccountNumber;
use App\ValueObject\BankAccountId;
use App\Event\AccountWasCreated;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Symfony\Component\Messenger\MessageBusInterface;
use UnexpectedValueException;

final class AccountNumberAssignmentConsumer implements Consumer
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function handle(Message $message): void
    {
        $event = $message->event();
        if ($event instanceof AccountWasCreated) {
            $this->messageBus->dispatch(new AssignAccountNumber($this->getBankAccountIdFromMessage($message)));
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
