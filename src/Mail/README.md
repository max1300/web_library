# Les mails avec Symfony
---

## SymfonyMailer
La classe `SymfonyMailer` permet d'implémenter le bundle du même nom. Ce bundle permet d'envoyer des mails via l'application. 

Afin de mettre en place l'envoi des mails, il faut faire appel à plusieurs composants du bundle. 
1. `MailerInterface`
2. `TransportInterface`
3. `Twig`

La variable `$ADMIN_EMAIL` permet d'initialiser l'addresse du destinataire depuis un seul endroit (**ici le fichier .env.local**)

```php
/**
     * ADMIN_EMAIL variable
     */
    private $ADMIN_EMAIL;
```

Une fois le bundle configuré, il ne reste qu'à créer les méthodes qui seront utilisées à divers endroits dans l'application pour des besoins d'envois différents. 

Toutes ces méthodes suivent une construction quasi similaire, on en présentera donc une seule. 

```php
public function sendEmailConfirmation(User $user)
    {
        //permet de construire le corps de l email via le template twig
        $body = $this->twig->render(
            'email/confirmation.html.twig',
            [
                'user' => $user
            ]
        );
        
        // on crée un nouvel objet email
        // cela permet de configurer correctement l'email
        // Qui envoit le mail, a qui on envoit l'email, le sujet et enfin le corps (body)
        $email = (new Email())
            ->from('webster-no-reply@gmail.com')
            ->to($user->getEmail())
            ->subject('Confirmation du compte')
            ->text($body, 'text/html');

        // pour finir une fois le mail créé, on envoit l'email
        $this->mailer->send($email);
    }

```
