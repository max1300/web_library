<?php

namespace App\Controller;

use App\Entity\User;
use App\Mail\SymfonyMailer;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
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
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json(array(
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
        ));
    }

    /**
     * @Route("/mail-reset-password", name="reset")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function forgotPassword(Request $request, UserPasswordEncoderInterface $encoder)
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
     * @Route("/reset-forgot-password", name="reset-forgot-password")
     * @param Request $request
     * @return Response
     */
    public function resetForgotPassword(Request $request)
    {
        if ($request->isMethod("PUT")) {
            $user_id = $request->query->get('user');
            $token = $request->query->get('token');
            $newPassword = json_decode($request->getContent(), true);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_id]);

            if ($user !== null && $token === $user->getForgotPasswordToken())
            {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword["newPassword"]));
                $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
                $this->entityManager->flush();
            }
        }
        return new Response("OK");
    }
}
