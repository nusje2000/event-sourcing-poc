<?php

declare(strict_types=1);

namespace App\Consumer\BankAccount;

use App\Entity\AccountInformation;
use App\Entity\BankAccount;
use App\Entity\BankAccountId;
use App\Event\BankAccount\AccountNumberWasAssigned;
use App\Event\BankAccount\CurrencyWasDeposited;
use App\Event\BankAccount\CurrencyWasWithdawn;
use App\Repository\AccountInformationRepository;
use App\ValueObject\Currency;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

final class AccountInformationProjectionConsumer implements Consumer
{
    /**
     * @var AccountInformationRepository
     */
    private $informationRepository;

    /**
     * @param AggregateRootRepository<BankAccount> $accountRepository
     */
    public function __construct(AccountInformationRepository $informationRepository)
    {
        $this->informationRepository = $informationRepository;
    }

    public function handle(Message $message): void
    {
        $event = $message->event();
        $id = $message->aggregateRootId();

        if (!$id instanceof BankAccountId) {
            return;
        }

        if ($event instanceof AccountNumberWasAssigned) {
            $information = new AccountInformation($id, $event->accountNumber(), Currency::createFromCents(0));
            $this->informationRepository->save($information);
        }

        if ($event instanceof CurrencyWasDeposited) {
            $information = $this->informationRepository->byAccount($id);
            $information->setBalance($information->balance()->add($event->amount()));
            $this->informationRepository->save($information);
        }

        if ($event instanceof CurrencyWasWithdawn) {
            $information = $this->informationRepository->byAccount($id);
            $information->setBalance($information->balance()->subtract($event->amount()));
            $this->informationRepository->save($information);
        }
    }
}
