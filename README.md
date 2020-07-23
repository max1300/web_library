# EventSubscriberDocumentation

![Image of Yaktocat](https://octodex.github.com/images/yaktocat.png)

L'application back de symfony dispose de 3 subscribers qui ont chacun pour rôle d'écouter des évènements bien déterminés.
(**Documentation sur les subscribers =** [docsubscriber](https://symfony.com/doc/4.2/doctrine/event_listeners_subscribers.html))

***

## Fonctionnement général des subscribers dans l'application
***Conception***
1. Afin de fonctionner le subscriber doit en premier lieu implémenter l'interface **EventSubscriberInterface**
```sh
class BlablaSubscriber implements EventSubscriberInterface
```

2. Le subscriber doit disposer d'un constructeur lui permettant notamment d'appeler des composants tiers de l'application comme des interfaces, des services, etc...
```sh
public function __construct(BlobloInterface $bloblo)
    {
        $this->bloblo = $bloblo;
    }
```

3. Il faut définir quel type d'évènement sera écouté par le subscriber et quel méthode ira se brancher sur cet évènement.
```sh
public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedBloblo', EventPriorities::PRE_WRITE]
        ];
    }
```

4. La déclaration de la méthode (**Voir les subscribers en dessous**)

***
## AuthorEntitySubscriber.php

***Buts*** :
1. Générer automatiquement le champ user lors de la création d'une ressource
2. Générer automatiquement le champ user lors de la création d'un commentaire

Les entités **Comment** et **Ressource** possèdent un champ *user* qui est une relation *ManyToOne* vers l'entité **User**. Ainsi un *user* authentifié devrait pouvoir créer une nouvelle *ressource* ou ajouter un nouveau *commentaire* et le champ *user* des deux entités devrait être rempli automatiquement avec l'*id* de l'utilisateur authentifié.

```sh
public function getAuthenticatedUser(ViewEvent $event)
    {
        // get controller results    
        $entity = $event->getControllerResult();
        
        // get request method
        $method = $event->getRequest()->getMethod();

        // check si l entité implément bien l interface et si la method est une requête POST
        if ($entity instanceof AuthorEntityInterface && Request::METHOD_POST === $method)
        {
            // récupère l utilisateur à partir du JWT Token puis rempli le champ user de
            // l entité avec l id de cet utilisateur
            $author = $this->tokenStorage->getToken()->getUser();
            $entity->setUser($author);
        }
    }
```

***
## RegisterSubscriber.php

***Buts*** :
Lors de la création d'un nouveau **User** :
1. Encode le mot de passe reçu depuis le formulaire
2. Génère un *token* dédié à la récupération du mot de passe lors de la phase de *login*
3. Génère un *token* de confirmation pour l"activation du compte lors de l'inscription
4. Envoi le mail pour confirmer l'activation du compte


```sh
 public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        // check si l entité concerné est bien User et si la méthode est une requête POST
        if(!$user instanceof User || $method !== Request::METHOD_POST) {
            return;
        }
        
        // Encode le mot de passe
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        
        // Génère le token d oubli de mot de passe
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        
        // Génère le token pour activer le compte utilisateur
        $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());
        
        // Envoi le mail en faisant appel au service Mailer créé
        $this->symfonyMailer->sendEmailConfirmation($user);
    }
```

***
## UserConfirmationSubscriber.php

***Buts*** :
Après la création d'un nouveau **User**, le travail du *RegisterSubscriber* et que l'utilisateur ait cliqué sur le lien d'activation du compte dans le mail reçu,  le *UserConfirmationSubscriber* se charge :
1. De retrouver l'utilisateur lié au *token* reçu
2. D'activer son compte

Il s'agit donc d'un évènement qui survient en *Post_Validate* et non plus en *Pre_Write*

```sh
public static function getSubscribedEvents()
    {
        return[
            KernelEvents::VIEW => ['userConfirmation', EventPriorities::POST_VALIDATE]
        ];
    }

 public function userConfirmation(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        
        // check si la requête est bien une requête GET et correspond à la route suivante
        if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
            return;
        }

        // récuèpre le token de confirmation reçu depuis l'email
        $tokenConfirmation = $event->getControllerResult();

        // appel le service confirmUser afin de retrouver l'utilisateur
        // et de passer le champ isEnabled à true de qui active le compte
        // utilisateur
        $this->confirmationService->confirmUser(
            $tokenConfirmation->tokenConfirmation
        );

        // renvoie une JsonResponse tout est ok Messire!
        $event->setResponse(new JsonResponse(null, Response::HTTP_OK));
    }
```

#### UserConfirmationService.php

```sh
public function confirmUser(string $tokenConfirmation)
    {
        // recherche l utilisateur lié au token
        $user = $this->repository->findOneBy(['tokenConfirmation' => $tokenConfirmation]);

        // si le user est pas trouvé renvoie une exception
        if (!$user) {
            throw new NotFoundHttpException();
        }
        
        // active le compte
        $user->setEnabledAccount(true);
        
        // passe le token de confirmation à null comme le compte est desormais activé
        $user->setTokenConfirmation(null);
        
        $this->entityManager->flush();
    }
```