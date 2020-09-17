<?php

namespace App\EventSubscriber;

use App\Mail\SymfonyMailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\UserRegisteredEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



//Permet d'envoyer le mail de confirmation
// il s'agit d'un symfony eventsubscriber
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
        //on indique à Symfony que l'événement qui sera déclenché ici
        //sera un évènement personnalisé qui et qui ira récuperer le
        //nom de l'event et appellera la fonction onUserRegisteredEvent 
        return array(
            UserRegisteredEvent::NAME => 'onUserRegisteredEvent'
        );
    }
    
    public function onUserRegisteredEvent(UserRegisteredEvent $event)
    {

        //on recupère l'entity sur laquelle l'évènement s'est déclenché
        $user = $event->getUserRegistered();

        //si cette entity n'est pas une entity User alors on ne retourne rien
        if(!$user instanceof User) {
            return;
        }

        //sinon si on est bien sur une entity User alors on :
        //encode le mot de passe
        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
        $user->setPlainPassword("");
        //on construit le token pour récupérer le mot de passe si oublié lors du login
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        //on construit le token qui va servir lors de la confirmation du compte
        $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());
        //on envoit l'email contenant le token pour la confirmation du compte
        $this->symfonyMailer->sendEmailConfirmation($user);
    }
}