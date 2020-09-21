<?php

namespace App\Controller;


use App\Entity\ForgotPassword;
use App\Form\ResetPasswordType;
use App\Form\UserType;
use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ForgotPasswordController extends AbstractController
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

        if ($user === null) {
            return new Response("NOT OK", Response::HTTP_BAD_REQUEST);
        }

        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailer->sendEmailForgotPassword($user);
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
        //recuperation du nouveau mot de passe envoyé depuis react
        $content = json_decode($request->getContent(), true);

        $user = $repository->findOneBy(['forgotPasswordToken' => $token]);

        if($user === null){
            return new Response("User not found", Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->submit($content);

        if (!$form->isValid()){
            $errors = $this->getErrors($form);
            return new Response(json_encode($errors), Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->setPlainPassword("");
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new Response("OK");

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
