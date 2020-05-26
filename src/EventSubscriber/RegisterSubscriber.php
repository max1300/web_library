<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;

use App\Mail\SymfonyMailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * RegisterSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGenerator $tokenGenerator
     * @param SymfonyMailer $mailer

     */
    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator,
        SymfonyMailer $mailer
    )
    {
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->symfonyMailer = $mailer;
    }

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

        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));

        $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());

        $this->symfonyMailer->sendEmailConfirmation($user);
    }
}