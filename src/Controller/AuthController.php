<?php

namespace App\Controller;

use App\Entity\ForgotPassword;
use App\Entity\User;
use App\Form\UserType;
use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        if ($user === null){
            return $this->json('user not identified', 400);
        }
        return $this->json([
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
            'login'=>$user->getLogin()
        ]);
    }

    public function register(Request $request)
    {
        //on récupère le contenu de l'objet JSON
        $content = json_decode($request->getContent(), true);

        //Creation d'un nouvel utilisateur
        $user = new User();

        //on crée un formulaire symfony d'enregistrement de user tel que cela a été
        //défini dans la classe Usertype du package form
        $form = $this->createForm(UserType::class, $user);
        //on insère les données du formulaire react dans le formulaire symfony
        //si le nom des champs ne correspondent pas on aura une erreur
        $form->submit($content);

        //on check la validité de chaque champ contenu dans le formulaire
        //on laisse la main à l'entity user pour le login et l'email
        //par contre on prend la main dans le formulaire sur les contraintes de validation sur le champ plainPassword
        //afin de s'assurer que le mot de passe du formulaire react se conforme bien aux contraintes de validité (voir dans UserType)
        if (!$form->isValid()) {
            $errors = $this->getErrors($form);
            throw new BadRequestHttpException(json_encode($errors));
            //si on détecte une contrainte de validation non respectée, on lance une reponse JSON vers le front
//            return $this->json($errors, 400);
        }

        //Ici intervient le registerSubscriber qui est un doctrine subscriber
        //et qui va encoder le mot de passe, créé le token pour envoyer la confirmation
        //et envoyer l'email pour l'activation du compte

        //sinon on fait persister le nouveau user créé
        $this->entityManager->persist($user);
        //puis on l'enregistre en BDD
        $this->entityManager->flush();

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

        if ($user === null || $token !== $user->getForgotPasswordToken()) {
            return new Response("user not identified or token not equal to forgotPasswordToken", 400);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        $this->entityManager->flush();
        return new Response("OK");

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

    private function getErrors(FormInterface $form): array
    {
        $errors = [];
        $formErrors = $form->getErrors(true);

        foreach ($formErrors as $error) {
            $field = $error->getOrigin()->getName();
            $message = $error->getMessage();

            $errors[$field] = $message;
        }

        return $errors;
    }
}
