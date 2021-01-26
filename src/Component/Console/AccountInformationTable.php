<?php

declare(strict_types=1);

namespace App\Component\Console;

use App\Entity\BankAccount;
use App\ValueObject\AccountNumber;
use App\ValueObject\Currency;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class AccountInformationTable
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var BankAccount
     */
    private $account;

    private function __construct(OutputInterface $output, BankAccount $account)
    {
        $this->output = $output;
        $this->account = $account;
    }

    public static function createFromAccount(OutputInterface $output, BankAccount $account): self
    {
        return new self($output, $account);
    }

    public function render(): void
    {
        $table = new Table($this->output);
        $table->setHeaders([]);
        $table->setRows($this->rows());
        $table->render();
    }

    /**
     * @return array<array<string>>
     */
    private function rows(): array
    {
        return [
            ['ID', $this->account->id()->toString()],
            ['Account Number', $this->formatAccountNumber($this->account->accountNumber())],
            ['Balance', $this->formatCurrency($this->account->currency())],
        ];
    }

    private function formatCurrency(Currency $currency): string
    {
        $value = sprintf('%+.2f', $currency->inEuros());

        if ($currency->isPositive()) {
            return sprintf('<positive>%s</positive>', $value);
        }

        return sprintf('<negative>%s</negative>', $value);
    }

    private function formatAccountNumber(?AccountNumber $accountNumber): string
    {
        if (null === $accountNumber) {
            return 'unassigned';
        }

        return $accountNumber->get();
    }
}
