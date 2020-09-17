<?php

namespace App\Controller;


use App\Entity\ForgotPassword;
use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ForgotPasswordController  
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
     * LoginController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SymfonyMailer $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     */
    public function __construct(
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
     * @Route("/mail-reset-password", name="reset", methods={"POST"})
     * @param Request $request
     * @param UserRepository $repository
     * @return Response
     */
    public function forgotPassword(Request $request, UserRepository $repository)
    {
        $mail = json_decode($request->getContent(), true);

        $user = $repository->findOneBy(['email' => $mail]);


        if ($user !== null)
        {
            $this->mailer->sendEmailForgotPassword($user);
            return new Response("OK");
        } else {
            return new Response("NOT OK");
        }

    }


    /**
     * @Route("/reset-forgot-password/{token}", name="reset-forgot-password", methods={"PUT"})
     * @param string $token
     * @param Request $request
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function resetForgotPassword(
        string $token,
        Request $request,
        UserRepository $repository,
        ValidatorInterface $validator
    )
    {
        //recuperation du nouveau mot de passe envoyé depuis react
        $password = json_decode($request->getContent(), true);

        //creation d'un objet forgotPassword et insertion du nouveau password react dans le champ password de l'objet
        $forgotPassword = new ForgotPassword();
        $forgotPassword->setPassword($password['password']);

        $passwordError = $validator->validateProperty($forgotPassword, 'password');
        $formErrors = [];
        if (empty($passwordError)) {
            $formErrors['passwordError'] = $passwordError[0]->getMessage();
        }

        if ($formErrors) {
            return new Response($formErrors['passwordError'], Response::HTTP_BAD_REQUEST);
        } else {
            //recuperation de l'utilisateur lié au token et à la demande de mot de passe oublié
            $user = $repository->findOneBy(['forgotPasswordToken' => $token]);

            if ($user === null || $token !== $user->getForgotPasswordToken()) {
                return new Response("user not identified or token not equal to forgotPasswordToken", 400);
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
            $this->entityManager->flush();
            return new Response("OK");
        }
    }

}
