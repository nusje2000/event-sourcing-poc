<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Entity\AccountInformation;
use App\Event\AccountNumberWasAssigned;
use App\Event\CurrencyWasChanged;
use App\Repository\AccountInformationRepository;
use App\ValueObject\BankAccountId;
use App\ValueObject\Currency;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

final class AccountInformationProjectionConsumer implements Consumer
{
    /**
     * @var AccountInformationRepository
     */
    private $informationRepository;

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

        if ($event instanceof CurrencyWasChanged) {
            $information = $this->informationRepository->byAccount($id);
            $information->setBalance($information->balance()->add($event->amount()));
            $this->informationRepository->save($information);
        }
    }
}
