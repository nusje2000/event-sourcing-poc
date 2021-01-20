<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\AggregateRootNotFound;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Finder\Finder;

use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\mkdir;
use function Safe\preg_replace;
use function Safe\sprintf;

final class FileSystemRepository implements MessageRepository
{
    /**
     * @var MessageSerializer
     */
    private $messageSerializer;

    /**
     * @var string
     */
    private $rootDirectory;

    public function __construct(MessageSerializer $messageSerializer, string $aggregateName, string $rootDirectory)
    {
        $this->messageSerializer = $messageSerializer;
        $this->rootDirectory = $rootDirectory . '/' . preg_replace('/[^a-z0-9]+/i', '_', $aggregateName);
    }

    public function persist(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $id = $message->aggregateRootId();
            $this->verifyAggregateRootDirectoryExists($id);
            $path = $this->messageFile($message);

            file_put_contents($path, json_encode($this->messageSerializer->serializeMessage($message), JSON_PRETTY_PRINT));
        }
    }

    public function retrieveAll(AggregateRootId $id): Generator
    {
        $files = $this->findFilesByAggregateRootId($id);

        foreach ($files as $file) {
            foreach ($this->messageSerializer->unserializePayload(json_decode($file->getContents(), true)) as $message) {
                yield $message;
            }
        }

        return isset($message) ? $message->aggregateVersion() : 0;
    }

    public function retrieveAllAfterVersion(AggregateRootId $id, int $aggregateRootVersion): Generator
    {
        $files = $this->findFilesByAggregateRootId($id);

        foreach ($files as $file) {
            foreach ($this->messageSerializer->unserializePayload(json_decode($file->getContents(), true)) as $message) {
                if ($message->aggregateVersion() <= $aggregateRootVersion) {
                    continue;
                }

                yield $message;
            }
        }

        return isset($message) ? $message->aggregateVersion() : 0;
    }

    private function verifyAggregateRootDirectoryExists(?AggregateRootId $id): void
    {
        $path = $this->aggregateRootDirectory($id);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function messageFile(Message $message): string
    {
        $eventId = $message->header(Header::EVENT_ID) ?? Uuid::uuid4()->toString();

        return sprintf('%s/v%05d_%s.json', $this->aggregateRootDirectory($message->aggregateRootId()), $message->aggregateVersion(), $eventId);
    }

    private function aggregateRootDirectory(?AggregateRootId $id): string
    {
        if (null === $id) {
            return $this->rootDirectory() . '/unknown_root';
        }

        return $this->rootDirectory() . '/root_' . $id->toString();
    }

    private function rootDirectory(): string
    {
        return $this->rootDirectory;
    }

    private function findFilesByAggregateRootId(AggregateRootId $id): Finder
    {
        if (!is_dir($this->aggregateRootDirectory($id))) {
            throw AggregateRootNotFound::byId($id);
        }

        return Finder::create()->in($this->aggregateRootDirectory($id))->name('*.json');
    }
}
