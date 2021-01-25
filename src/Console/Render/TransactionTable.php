<?php

declare(strict_types=1);

namespace App\Console\Render;

use App\Entity\Transaction;
use App\ValueObject\Currency;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class TransactionTable
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var array<Transaction>
     */
    private $transactions;

    /**
     * @param array<Transaction> $transactions
     */
    private function __construct(OutputInterface $output, array $transactions)
    {
        $this->output = $output;
        $this->transactions = $transactions;
    }

    /**
     * @param array<Transaction> $transactions
     */
    public static function createFromTransactions(OutputInterface $output, array $transactions): self
    {
        return new self($output, $transactions);
    }

    public function render(): void
    {
        if (count($this->transactions) < 1) {
            return;
        }

        $table = new Table($this->output);
        $table->setHeaders($this->headers());
        $table->setRows($this->rows());
        $table->render();
    }

    /**
     * @return array<string>
     */
    private function headers(): array
    {
        return ['Transaction ID', 'Amount', 'Date'];
    }

    /**
     * @return array<array<string>>
     */
    private function rows(): array
    {
        return array_map(function (Transaction $transaction): array {
            return [
                $transaction->id()->toString(),
                $this->formatCurrency($transaction->amount()),
                $transaction->timestamp()->format('Y-m-d H:i:s'),
            ];
        }, $this->transactions);
    }

    private function formatCurrency(Currency $currency): string
    {
        $value = sprintf('%+.2f', $currency->inEuros());

        if ($currency->isPositive()) {
            return sprintf('<positive>%s</positive>', $value);
        }

        return sprintf('<negative>%s</negative>', $value);
    }
}
