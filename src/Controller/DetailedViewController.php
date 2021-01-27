<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AccountInformation;
use App\Entity\BankAccount;
use App\Entity\Transaction;
use App\Repository\AccountInformationRepository;
use App\Repository\TransactionRepository;
use App\ValueObject\BankAccountId;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DetailedViewController extends AbstractController
{
    /**
     * @var AggregateRootRepository<BankAccount>
     */
    private $rootRepository;

    /**
     * @var AccountInformationRepository
     */
    private $accountInformationRepository;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var ClassNameInflector
     */
    private $classNameInflector;

    /**
     * @var SynchronousMessageDispatcher
     */
    private $messageDispatcher;

    /**
     * @param AggregateRootRepository<BankAccount> $rootRepository
     */
    public function __construct(
        AggregateRootRepository $rootRepository,
        MessageRepository $messageRepository,
        AccountInformationRepository $accountInformationRepository,
        TransactionRepository $transactionRepository,
        ClassNameInflector $classNameInflector,
        SynchronousMessageDispatcher $messageDispatcher
    ) {
        $this->rootRepository = $rootRepository;
        $this->messageRepository = $messageRepository;
        $this->accountInformationRepository = $accountInformationRepository;
        $this->transactionRepository = $transactionRepository;
        $this->classNameInflector = $classNameInflector;
        $this->messageDispatcher = $messageDispatcher;
    }

    /**
     * @Route("/account/view/{id}/detailed", name="app_account_detailed")
     */
    public function view(string $id): Response
    {
        $idObject = BankAccountId::fromString($id);

        return $this->render('detailed/index.html.twig', [
            'id' => $idObject,
            'aggregate' => $this->rootRepository->retrieve($idObject),
        ]);
    }

    /**
     * @Route("/account/view/{id}/detailed/transactions", name="app_account_detailed_transactions")
     */
    public function viewTransactions(string $id): Response
    {
        $idObject = BankAccountId::fromString($id);

        return $this->render('detailed/transactions.html.twig', [
            'id' => $idObject,
            'class' => Transaction::class,
            'source' => $this->transactionRepository,
            'transactions' => $this->transactionRepository->byAccount($idObject),
        ]);
    }

    /**
     * @Route("/account/view/{id}/detailed/account-information", name="app_account_detailed_account_information")
     */
    public function viewAccountInformation(string $id): Response
    {
        $idObject = BankAccountId::fromString($id);

        return $this->render('detailed/account_information.html.twig', [
            'id' => $idObject,
            'class' => AccountInformation::class,
            'source' => $this->accountInformationRepository,
            'information' => $this->accountInformationRepository->byAccount($idObject),
        ]);
    }

    /**
     * @Route("/account/view/{id}/detailed/messages", name="app_account_detailed_messages")
     */
    public function viewMessages(string $id): Response
    {
        $idObject = BankAccountId::fromString($id);

        return $this->render('detailed/messages.html.twig', [
            'id' => $idObject,
            'source' => $this->messageRepository,
            'messages' => $this->messageRepository->retrieveAll($idObject),
            'inflector' => $this->classNameInflector,
        ]);
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        return parent::render($view, array_merge([
            'aggregate_root_repository_class' => get_class($this->rootRepository),
            'message_dispatcher_class' => get_class($this->messageDispatcher),
            'consumers' => $this->getConsumers(),
            'events' => $this->getEvents(),
        ], $parameters), $response);
    }

    /**
     * @return array<Consumer>
     */
    private function getConsumers(): array
    {
        $reflection = new ReflectionObject($this->messageDispatcher);
        $property = $reflection->getProperty('consumers');
        $property->setAccessible(true);
        $consumers = $property->getValue($this->messageDispatcher);
        $property->setAccessible(false);

        return $consumers;
    }

    /**
     * @return array<string>
     */
    private function getEvents(): array
    {
        $finder = Finder::create()->in(dirname(__DIR__) . '/Event')->files();
        $events = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $events['src/Event/' . $file->getRelativePathname()] = str_replace('.php', '', $file->getRelativePathname());
        }

        return $events;
    }
}
