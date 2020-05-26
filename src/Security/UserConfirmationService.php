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

    public function confirmUser(string $tokenConfirmation)
    {
        $user = $this->repository->findOneBy(['tokenConfirmation' => $tokenConfirmation]);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabledAccount(true);
        $user->setTokenConfirmation(null);
        $this->entityManager->flush();
    }

}