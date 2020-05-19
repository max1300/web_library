<?php


namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserConfirmationService constructor.
     * @param UserRepository $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function confirmUser(string $confirmationToken)
    {
        $user = $this->repository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabledAccount(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();
    }

}