<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use App\Mail\SymfonyMailer;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignUpController extends AbstractController
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
     * SignUpController constructor.
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
     * @Route("/sign_up", name="sign_up")
     * @param Request $request
     * @return Response
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
    {
        //1)build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            return $this->mailer->sendEmailConfirmation($user);
        }

        return new Response("Check your Mailbox for verify your email and activate your account");
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
}
