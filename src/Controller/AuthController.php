<?php

namespace App\Controller;

use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AuthController constructor.
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct
    (
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        Environment $twig,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->passwordEncoder = $passwordEncoder;
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function forgotPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($request->isMethod("POST"))
        {
            $mail = json_decode($request->getContent(), true);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $mail]);

            $body = $this->twig->render(
                'email/resetPasswordMail.html.twig',
                [
                    'user' => $user
                ]
            );

            if ($user !== null)
            {
                $message = (new \Swift_Message('Demande de réinitialisation de mot de passe'))
                    ->setFrom('webster@gmail.com')
                    ->setTo('ren.maxime@gmail.com')
                    ->setBody($body, 'text/html');

                $this->mailer->send($message);
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
            $newpassword = json_decode($request->getContent(), true);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user_id]);

            if ($user !== null)
            {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $newpassword["newPassword"]));
                $this->entityManager->flush();


            }
        }
        return new Response("OK");
    }
}
