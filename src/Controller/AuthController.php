<?php

namespace App\Controller;

use App\Entity\User;
use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    //pour modifier la base de donnée
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var SymfonyMailer
     */
    private $mailer;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * AuthController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SymfonyMailer $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct
    (
        EntityManagerInterface $entityManager,
        SymfonyMailer $mailer,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator
    )
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
    }


    /**
     * @Route("/api/login_check", name="login")
     * @return Response
     */
    public function login(): Response
    {
        $user = $this->getUser();
        return new Response([
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
            'login'=>$user->getLogin()
        ]);
    }

    /**
     * @Route("/mail-reset-password", name="reset")
     * @param Request $request
     * @return Response
     */
    public function forgotPassword(Request $request)
    {
        if ($request->isMethod("POST"))
        {
            $mail = json_decode($request->getContent(), true);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $mail]);

            if ($user !== null)
            {
                $this->mailer->sendEmailForgotPassword($user);
            }
        }
        return new Response("OK");
    }

    /**
     * @Route("/reset-forgot-password/{token}", name="reset-forgot-password", methods={"PUT"})
     * @param string $token
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     */
    public function resetForgotPassword(string $token, Request $request, UserRepository $repository)
    {
        $newPassword = json_decode($request->getContent(), true);

        $user = $repository->findOneBy(['forgotPasswordToken' => $token]);

        // control pour verifier si newPassword existe

        if ($user !== null && $token === $user->getForgotPasswordToken())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword["newPassword"]));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
            $this->entityManager->flush();
        }
        return new Response("OK");
    }
}
