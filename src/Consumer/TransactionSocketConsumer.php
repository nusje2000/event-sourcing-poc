<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Event\CurrencyChangeWasBlocked;
use App\Event\CurrencyWasChanged;
use App\Repository\TransactionRepository;
use App\ValueObject\BankAccountId;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use UnexpectedValueException;

use function Safe\json_encode;
use function Safe\sprintf;

final class TransactionSocketConsumer implements Consumer
{
    private const UPDATE_PATH = '/update-transactions/%s';

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $host;

    /**
     * @var TransactionRepository
     */
    private $repository;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, TransactionRepository $repository, string $host)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->host = $host;
        $this->repository = $repository;
    }

    public function handle(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof CurrencyWasChanged) {
            $this->triggerUpdate($this->getBankAccountIdFromMessage($message));
        }

        if ($event instanceof CurrencyChangeWasBlocked) {
            $this->triggerUpdate($this->getBankAccountIdFromMessage($message));
        }
    }

    private function triggerUpdate(BankAccountId $id): void
    {
        $path = sprintf(self::UPDATE_PATH, $id->toString());
        $response = $this->client->request('POST', rtrim($this->host, '/') . $path, [
            'body' => json_encode($this->repository->byAccount($id)),
        ]);

        if (200 !== $response->getStatusCode()) {
            $this->logger->error('Updating socket failed.');
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
