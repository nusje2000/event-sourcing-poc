<?php

declare(strict_types=1);

namespace App\Component\EventSource;

use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

final class DebugSocketConsumer implements Consumer
{
    private const UPDATE_PATH = '/debug-event';

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
     * @var MessageSerializer
     */
    private $messageSerializer;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, MessageSerializer $messageSerializer, string $host)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->host = $host;
        $this->messageSerializer = $messageSerializer;
    }

    public function handle(Message $message): void
    {
        $response = $this->client->request('POST', rtrim($this->host, '/') . self::UPDATE_PATH, [
            'body' => json_encode($this->messageSerializer->serializeMessage($message)),
        ]);

        if (200 !== $response->getStatusCode()) {
            $this->logger->error('Debug failed.');
        }
    }
}
