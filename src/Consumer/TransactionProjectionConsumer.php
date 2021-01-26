<?php

declare(strict_types=1);

namespace App\Consumer;

use App\ValueObject\BankAccountId;
use App\Entity\Transaction;
use App\Event\CurrencyWasDeposited;
use App\Event\CurrencyWasWithdawn;
use App\Repository\TransactionRepository;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Ramsey\Uuid\Uuid;
use UnexpectedValueException;

final class TransactionProjectionConsumer implements Consumer
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function handle(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof CurrencyWasDeposited) {
            $this->transactionRepository->save(
                new Transaction(
                    Uuid::uuid4(),
                    $this->getBankAccountIdFromMessage($message),
                    $event->amount(),
                    $message->timeOfRecording()->dateTime()
                )
            );
        }

        if ($event instanceof CurrencyWasWithdawn) {
            $this->transactionRepository->save(
                new Transaction(
                    Uuid::uuid4(),
                    $this->getBankAccountIdFromMessage($message),
                    $event->amount()->multiply(-1),
                    $message->timeOfRecording()->dateTime()
                )
            );
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
