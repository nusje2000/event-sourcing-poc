<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\BankAccountId;
use App\ValueObject\Currency;
use EventSauce\EventSourcing\AggregateRootRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccountController extends AbstractController
{
    private AggregateRootRepository $repository;

    public function __construct(AggregateRootRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/account/create", name="app_account_create")
     */
    public function create(): Response
    {
        $account = BankAccount::initiateWithPredefinedAccountNumber(
            BankAccountId::generate(),
            sprintf('SA00AUSE%010d', random_int(0, 10 ** 10))
        );

        $this->repository->persist($account);

        return $this->redirectToRoute('app_account_view', [
            'id' => $account->id()->toString(),
        ]);
    }

    /**
     * @Route("/account/view/{id}", name="app_account_view")
     */
    public function view(string $id): Response
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve(BankAccountId::fromString($id));

        return $this->render('overview.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @Route("/account/deposit/{id}/{amount}", name="app_account_deposit")
     */
    public function deposit(string $id, int $amount): Response
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve(BankAccountId::fromString($id));
        $account->deposit(Currency::createFromCents($amount));
        $this->repository->persist($account);

        return $this->redirectToRoute('app_account_view', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/account/withdraw/{id}/{amount}", name="app_account_withdraw")
     */
    public function withdraw(string $id, int $amount): Response
    {
        /** @var BankAccount $account */
        $account = $this->repository->retrieve(BankAccountId::fromString($id));
        $account->withdraw(Currency::createFromCents($amount));
        $this->repository->persist($account);

        return $this->redirectToRoute('app_account_view', [
            'id' => $id,
        ]);
    }
}
