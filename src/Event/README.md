# Les Events et l'Event Dispatcher
--------

## Introduction:
-------- 
Au cours du développement de l'application nous avons fait face à une problématique. Lorsqu'un développeur installait l'application sur sa machine en Local et qu'il chargait les fixtures, il recevait un email de confirmation pour chaque utilisateur inscrit en base données. Ce qui pourrait vite devenir embetant si l'on devait avoir des centaines ou encore des milliers d'utilisateurs. Cela était du au fait que notre suscriber implémentait l'Event Suscriber de Doctrine. Afin de répondre à cette problématique Nous avons opté pour une solution qui nous a permis d'extraire l'envoie d'email de Doctrine. Pour cela nous avons du agir sur plusieurs fichiers de l'application, le fichier RegisterController.php, le fichier RegisterSuscriber.php, le fichier AppFixtures.php. Nous avons également du créer un fichier UserRegisteredEvent.php qui réprésente notre propre évènement Symfony. Vous trouverez une explication détaillée des opérations effectuées. 

### 1 - L'évenement:
--------- 
Dans un premier temps nous avons donc choisi de créer notre propre évènement Symfony, cette classe est un Plain Old PHP Object (POPO), c'est une classe simple c'est à dire qu'elle n'implémente aucune interface, qu'il n'est pas nécéssaire d'en faire une classe étendue ou chaine d'héritage pour stocker des données ou exécuter une logique. Le POPO vient à la base de l'idée de POJO(Plain Old JAVA Object) qui est un objet Java ordinaire, non lié par une restriction particulière et ne nécessitant aucun chemin de classe. Notre évènement implémente donc uniquement l'Entity User, elle possède  une constante publique, un attribut protégé $UserRegistered, un constructor qui prend en paramètres l'objet User et retourne la valeur $UserRegistered. Nous avons donc "type Hinter" l'objet User c'est à dire que Ce paramètre est d'un certains type lors de l'appel du constructeur dans la fonction permettant d'accéder à l'utilisateur inscrit. Voici un extrait de code:

```php
    
    namespace App\Event;

    use App\Entity\User;

    class UserRegisteredEvent
    {
      public const NAME = 'user.register';
  
      protected $userRegistered;

      public function __construct(User $userRegistered)
      {
      $this->userRegistered = $userRegistered;
      }

      public function getUserRegistered()
      {
        return $this->userRegistered;
      }
    }
```
### 2 - Le Controller:
---------
Le register controller va servir à la création d'un nouvel utilisateur grâce à au suscriber qui est un symfony subscriber, à la validation des champs renseigné dans le formulaire, va creer un nouvel Evenement Personnalisé. Le dispatcher au Suscriber, puis enregistré l'utilisateur en base de données. Voici un de code: 

```php

    <?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RegisterController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    //pour modifier la base de donnée
    private $entityManager;


    /**
     * RegisterController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }



    public function register(Request $request, EventDispatcherInterface $dispatcher)
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
        }

        //Ici on crée un nouvel Event Personnalisé
        //qui prend en paramètres un objet User ensuite 
        //cet evenément sera dispatcher au Suscriber
        //c'est à qu'il écoutera cet event en particulier  
        $event = new UserRegisteredEvent($user);
        $dispatcher->dispatch($event, UserRegisteredEvent::NAME);
        //on fait persister le nouveau user créé
        $this->entityManager->persist($user);
        //puis on l'enregistre en BDD
        $this->entityManager->flush();

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }

```
### 3 - Le Suscriber:
---------
Le Register Suscriber est en charge d'écouter l'Event personnalisé créer par le Controller et de réaliser des opérations à chaque qu'il recoit un nouvel Event. Il sera chargé d'encoder le mot de passe, de créer le token et d'envoyer le mail d'activation pour chaque utilisateur qui remplira le formulaire d'inscription. Voici un extrait de code : 

```php
    namespace App\EventSubscriber;

    use App\Mail\SymfonyMailer;
    use App\Entity\User;
    use App\Security\TokenGenerator;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use App\Event\UserRegisteredEvent;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



    //Permet d'envoyer le mail de confirmation
    // il s'agit d'un symfony eventsubscriber
    // les evenements se déclenche 
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
```
### 4 - Les Fixtures:
---------
Les Fixtures sont un ensemble de fausses données que l'on charge en base de données afin de pouvoir faire des tests ou fournir des données intéréssante à traiter pendant le développement de l'appplication [Cliquez-ici](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html) pour en savoir plus.

## Conclusion:
--------
En réalisant toutes ces étapes nous avons pu extraire notre suscriber de doctrine pour eviter que le mail soit envoyé aux futurs developpeurs qui devront intervernir sur l'application, lorsqu'il chargeront les fixtures. 