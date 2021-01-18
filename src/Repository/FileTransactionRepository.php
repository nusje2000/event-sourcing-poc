<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BankAccountId;
use App\Entity\Transaction;
use App\ValueObject\Currency;
use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Symfony\Component\Finder\Finder;

use function Safe\file_put_contents;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\mkdir;

final class FileTransactionRepository implements TransactionRepository
{
    /**
     * @var string
     */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return array<Transaction>
     */
    public function byAccount(BankAccountId $accountId): array
    {

        $accountDirectory = $this->accountDirectory($accountId);
        if (!is_dir($accountDirectory)) {
            return [];
        }

        $transactions = [];
        $finder = Finder::create()->in($accountDirectory)->name('*.json');
        foreach ($finder as $file) {
            $decoded = json_decode($file->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $transactions[] = new Transaction(
                Uuid::fromString($decoded['id']),
                BankAccountId::fromString($decoded['account_id']),
                Currency::createFromCents($decoded['amount']),
                DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $decoded['timestamp'])
            );
        }

        uasort($transactions, static function (Transaction $a, Transaction $b) {
            return $b->timestamp() <=> $a->timestamp();
        });

        return $transactions;
    }

    public function save(Transaction $transaction): void
    {
        $this->prepareDirectory($transaction);
        $transactionFile = $this->transactionFile($transaction);
        file_put_contents($transactionFile, json_encode($transaction, JSON_THROW_ON_ERROR));
    }

    public function delete(Transaction $transaction): void
    {
        $transactionFile = $this->transactionFile($transaction);
        if (file_exists($transactionFile)) {
            unlink($transactionFile);
        }
    }

    private function prepareDirectory(Transaction $transaction): void
    {
        $accountDirectory = $this->accountDirectory($transaction->accountId());
        if (!is_dir($accountDirectory)) {
            mkdir($accountDirectory, 0777, true);
        }
    }

    private function transactionFile(Transaction $transaction): string
    {
        return sprintf('%s/%s.json', $this->accountDirectory($transaction->accountId()), $transaction->id()->toString());
    }

    private function accountDirectory(BankAccountId $accountId): string
    {
        return $this->directory . '/' . $accountId->toString();
    }
}
