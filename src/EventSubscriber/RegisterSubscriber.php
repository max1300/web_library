<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Mail\SymfonyMailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


//Permet d'envoyer le mail
class RegisterSubscriber implements EventSubscriberInterface
{
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**

     * @var SymfonyMailer
     */
    private $symfonyMailer;
    /**
     * @var ValidatorInterface
     */
    private $validator;


    /**
     * RegisterSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGenerator $tokenGenerator
     * @param SymfonyMailer $mailer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator,
        SymfonyMailer $mailer,
        ValidatorInterface $validator
    )
    {
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->symfonyMailer = $mailer;
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]

        ];
    }

    public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || $method !== Request::METHOD_POST) {
            return;
        }
        $errors = $this->validator->validate($user);

        if (empty($errors)){
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());

            $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());

            $this->symfonyMailer->sendEmailConfirmation($user);
        }

    }
}