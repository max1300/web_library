<?php

namespace App\Controller;

use App\Entity\ForgotPassword;
use App\Entity\User;
use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @Route("/api/login_check", name="login")
     * @param Request $request
     * @param UserRepository $repository
     * @return JsonResponse
     */
    public function login(Request $request, UserRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $repository->findOneBy(['email' => $data["username"]]);
        return $this->json([
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
            'login'=>$user->getLogin()
        ]);
    }

    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        //on récupère le contenu de l'objet JSON
        $content = json_decode($request->getContent(), true);

        // on insere chaque champ de l'objet JSON dans un champ distinct
        $username = $content['login'];
        $mail = $content['email'];
        $password = $content['password'];

        //Creation d'un nouvel utilisateur
        $user = new User();
        $user->setEmail($mail);
        $user->setLogin($username);
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());
        //on persist le user
        $this->entityManager->persist($user);
        //on enregistre dans la BDD
        $this->entityManager->flush();

        //on envoit l'email de confirmation
        $this->mailer->sendEmailConfirmation($user);
        return new Response(sprintf('User %s successfully created', $user->getUsername()));
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
        }

        return new Response("OK");
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
        $forgotPassword = $this->createNewForgotPassword($password);

        $passwordError = $validator->validateProperty($forgotPassword, 'password');
        $formErrors = $this->getFormError($passwordError);

        if ($formErrors) {
            return new Response($formErrors['passwordError']);
        }

        //recuperation de l'utilisateur lié au token et à la demande de mot de passe oublié
        $user = $repository->findOneBy(['forgotPasswordToken' => $token]);

        if ($user !== null && $token === $user->getForgotPasswordToken())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
            $this->entityManager->flush();
            return new Response("OK");
        }
    }

    /**
     * @param ConstraintViolationListInterface $passwordError
     * @return array
     */
    public function getFormError(ConstraintViolationListInterface $passwordError): array
    {
        $formErrors = [];
        if (count($passwordError) > 0) {
            $formErrors['passwordError'] = $passwordError[0]->getMessage();
        }
        return $formErrors;
    }

    /**
     * @param $password
     * @return ForgotPassword
     */
    public function createNewForgotPassword($password): ForgotPassword
    {
        $forgotPassword = new ForgotPassword();
        $forgotPassword->setPassword($password['password']);
        return $forgotPassword;
    }
}
