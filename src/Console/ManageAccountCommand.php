<?php

declare(strict_types=1);

namespace App\Console;

use App\Command\BankAccount\Deposit;
use App\Command\BankAccount\Withdraw;
use App\Console\Render\AccountInformationTable;
use App\Console\Render\TransactionTable;
use App\Entity\BankAccount;
use App\Entity\BankAccountId;
use App\Repository\TransactionRepository;
use App\ValueObject\Currency;
use EventSauce\EventSourcing\AggregateRootRepository;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnexpectedValueException;

final class ManageAccountCommand extends Command
{
    protected static $defaultName = 'app:bank_account:manage';

    /**
     * @var AggregateRootRepository
     */
    private $rootRepository;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var SymfonyStyle|null
     */
    private $io;

    public function __construct(AggregateRootRepository $rootRepository, TransactionRepository $transactionRepository, CommandBus $commandBus)
    {
        parent::__construct();

        $this->rootRepository = $rootRepository;
        $this->transactionRepository = $transactionRepository;
        $this->commandBus = $commandBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = BankAccountId::fromString((string) $input->getArgument('id'));
        $this->io = new SymfonyStyle($input, $output);

        $formatter = $this->io->getFormatter();
        $formatter->setStyle('positive', new OutputFormatterStyle('green'));
        $formatter->setStyle('negative', new OutputFormatterStyle('red'));

        while (true) {
            $this->clearDisplay();
            if (!$this->iterate($id)) {
                break;
            }
        }

        $this->clearDisplay();

        return 0;
    }

    protected function configure(): void
    {
        $this->setDescription('Manage the account with the given id.');
        $this->addArgument('id', InputArgument::REQUIRED, 'The id of the bank account that you would like to view.');
    }

    private function iterate(BankAccountId $id): bool
    {
        $this->displayAccountInformation($id);
        $this->displayTransactions($id);
        $action = $this->askForNextAction($id);

        if (null === $action) {
            return false;
        }

        $this->commandBus->handle($action);

        return true;
    }

    private function displayAccountInformation(BankAccountId $accountId): void
    {
        /** @var BankAccount $account */
        $account = $this->rootRepository->retrieve($accountId);
        AccountInformationTable::createFromAccount($this->io(), $account)->render();
    }

    private function displayTransactions(BankAccountId $accountId): void
    {
        $transactions = $this->transactionRepository->byAccount($accountId);
        TransactionTable::createFromTransactions($this->io(), $transactions)->render();
    }

    private function askForNextAction(BankAccountId $accountId): ?object
    {
        $choice = $this->io()->askQuestion(new ChoiceQuestion('What would you like to do?', ['deposit', 'withdraw', 'quit']));

        $this->clearDisplay();
        $this->displayAccountInformation($accountId);

        if ('deposit' === $choice) {
            return $this->handleDepositRequest($accountId);
        }

        if ('withdraw' === $choice) {
            return $this->handleWithdrawRequest($accountId);
        }

        return null;
    }

    private function handleWithdrawRequest(BankAccountId $accountId): Withdraw
    {
        return new Withdraw(
            $accountId,
            $this->askForCurrencyAmount('How much would you like to withdraw?', 'Are you sure you want to withdraw {amount} euros?')
        );
    }

    private function handleDepositRequest(BankAccountId $accountId): Deposit
    {
        return new Deposit(
            $accountId,
            $this->askForCurrencyAmount('How much would you like to deposit?', 'Are you sure you want to deposit {amount} euros?')
        );
    }

    private function askForCurrencyAmount(string $question, string $confirmationQuestion): Currency
    {
        $value = (float) $this->io()->ask($question);
        $confirmationQuestion = str_replace('{amount}', sprintf('%.2f', $value), $confirmationQuestion);

        if (!$this->io->confirm($confirmationQuestion)) {
            $this->clearDisplay();

            return $this->askForCurrencyAmount($question, $confirmationQuestion);
        }

        return Currency::createFromEuros($value);
    }

    private function io(): SymfonyStyle
    {
        if (null === $this->io) {
            throw new UnexpectedValueException('Expected io to be set, make sure to run the execute function to initialize this value.');
        }

        return $this->io;
    }

    private function clearDisplay(): void
    {
        $this->io()->write("\033\143");
    }
}
