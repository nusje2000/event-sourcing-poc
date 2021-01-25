<?php

declare(strict_types=1);

namespace App\Controller;

use App\Command\BankAccount\Create;
use App\Command\BankAccount\Deposit;
use App\Command\BankAccount\Withdraw;
use App\Entity\BankAccountId;
use App\Repository\AccountInformationRepository;
use App\Repository\TransactionRepository;
use App\ValueObject\Currency;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccountController extends AbstractController
{
    /**
     * @var AccountInformationRepository
     */
    private $informationRepository;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(
        AccountInformationRepository $informationRepository,
        TransactionRepository $transactionRepository,
        CommandBus $commandBus
    ) {
        $this->informationRepository = $informationRepository;
        $this->transactionRepository = $transactionRepository;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/account/create", name="app_account_create")
     */
    public function create(): Response
    {
        $id = BankAccountId::generate();
        $this->commandBus->handle(Create::withId($id));

        return $this->redirectToRoute('app_account_view', [
            'id' => $id->toString(),
        ]);
    }

    /**
     * @Route("/account/view/{id}", name="app_account_view")
     */
    public function view(string $id): Response
    {
        $accountId = BankAccountId::fromString($id);

        return $this->render('overview.html.twig', [
            'id' => $accountId,
            'account' => $this->informationRepository->byAccount($accountId),
            'transactions' => $this->transactionRepository->byAccount($accountId),
        ]);
    }

    /**
     * @Route("/account/deposit/{id}/{amount}", name="app_account_deposit")
     */
    public function deposit(string $id, int $amount): Response
    {
        $this->commandBus->handle(
            new Deposit(
                BankAccountId::fromString($id),
                Currency::createFromCents($amount)
            )
        );

        return $this->redirectToRoute('app_account_view', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/account/withdraw/{id}/{amount}", name="app_account_withdraw")
     */
    public function withdraw(string $id, int $amount): Response
    {
        $this->commandBus->handle(
            new Withdraw(
                BankAccountId::fromString($id),
                Currency::createFromCents($amount)
            )
        );

        return $this->redirectToRoute('app_account_view', [
            'id' => $id,
        ]);
    }
}
