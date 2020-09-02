<?php

namespace App\Mail;

use App\Entity\User;
use App\Entity\Contact;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\MailerInterface; 
use Symfony\Component\Mime\Email;
use Twig\Environment;

class SymfonyMailer
{

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TransportInterface
     */
    private $transport;

    /**
     * @var twig
     */
    private $twig;

    /**
     * ADMIN_EMAIL variable
     */
    private $ADMIN_EMAIL;


    /**
     * SymfonyMailer constructor.
     * @param MailerInterface $mailer
     * @param TransportInterface $transport
     * @param Environment $twig
     * @param string $ADMIN_EMAIL
     */
    public function __construct(
        MailerInterface $mailer,
        TransportInterface $transport,
        Environment $twig,
        string $ADMIN_EMAIL
    )
    {
        $this->mailer = $mailer;
        $this->transport = $transport;
        $this->twig = $twig;
        $this->ADMIN_EMAIL = $ADMIN_EMAIL;
    }

    public function sendEmailConfirmation(User $user)
    {
        $body = $this->twig->render(
            'email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );
        
        $email = (new Email())
            ->from('webster-no-reply@gmail.com')
            ->to($user->getEmail())
            ->subject('Confirmation du compte')
            ->text($body, 'text/html');

        $this->mailer->send($email);
    }

    public function sendEmailForgotPassword(User $user)
    {
        $body = $this->twig->render(
            'email/resetPasswordMail.html.twig',
            [
                'user' => $user
            ]
        );

        $email = (new Email())
            ->subject('Demande de réinitialisation de mot de passe')
            ->from('webster-no-reply@gmail.com')
            ->to($this->ADMIN_EMAIL)
            ->text($body, BODY_CHARSET);

        $this->mailer->send($email);
    }

    //send contact message
    public function sendContactMessage(Contact $contact)
    {
        $body = $this->twig->render(
            'contact/contact.html.twig',
            [
                'contact' => $contact
            ]
        );

        $email = (new Email())
            ->subject('Contact Message')
            ->from($contact->getEmail())
            ->to($this->ADMIN_EMAIL)
            ->text($body, BODY_CHARSET);

        $this->mailer->send($email);
    }

    
}