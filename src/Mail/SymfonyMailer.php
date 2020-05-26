<?php

namespace App\Mail;

use App\Entity\User;
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
     * SymfonyMailer constructor.
     * @param MailerInterface $mailer
     * @param TransportInterface $transport
     * @param Environment $twig
     */
    public function __construct(
        MailerInterface $mailer,
        TransportInterface $transport,
        Environment $twig
    )
    {
        $this->mailer = $mailer;
        $this->transport = $transport;
        $this->twig = $twig;
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
            ->to('ren.maxime@gmail.com')
            ->text($body, 'text/html');

        $this->mailer->send($email);
    }
}