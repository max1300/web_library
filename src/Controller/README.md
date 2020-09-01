# Les controller
---

## LoginController
Ce controller sert exclusivement aux fonctions d'authentification. On y retrouvera donc les fonctions de `login`, `register` et toutes les fonctions qui en découlent comme celles pour les mots de passes oubliés.

### *Login*
La fonction de login sert essentiellement à aider le bundle Lexik JWT à construire le token. 
On retrouve donc le `user` grâce à son email (`qui sert d'identifiant unique`) puis on passe certaines informations de ce `user` pour construire le token.

```php
/**
     * @Route("/api/login_check", name="login")
     * @return JsonResponse
     */
    public function login(Request $request, UserRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        //on retrouve le user qui cherche à s'authentifier
        $user = $repository->findOneBy(['email' => $data["username"]]);

        //on retourne certaines informations de ce user
        // à l'intérieur du token
        return $this->json([
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
            'login'=>$user->getLogin()
        ]);
    }
```
## ForgotPasswordController
La fonction de mot de passe oublié est divisé en 2 fonctions qui vont avoir chacune un rôle particulier.

La fonction `forgotPassword` va recevoir `l'email` de l'utilisateur via le front React. Grâce à ce mail, on va rechercher l'utilisateur en question afin de valider qu'il s'agisse d'un utilisateur inscrit. Une fois cela vérifié, on va construire un email via `SymfonyMailer` et envoyer ce mail à l'utilisateur avec un lien contenant un `token` de vérification. Ce lien va emmener l'utilisateur sur une nouvelle page `React` demandant à l'utilisateur de saisir son nouveau mot de passe. Cette page `React` va aussi se charger de récupérer le `token` présent dans le lien et de le renvoyer au Backend avec le nouveau mot de passe.

```php
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


        if ($user !== null)
        {
            $this->mailer->sendEmailForgotPassword($user);
        }

        return new Response("OK");
    }
```
Ce lien va emmener l'utilisateur sur une nouvelle page `React` demandant à l'utilisateur de saisir son nouveau mot de passe. Cette page `React` va aussi se charger de récupérer le `token` présent dans le lien et de le renvoyer au Backend avec le nouveau mot de passe.

La fonction `createNewForgotPassword` va pouvoir créer un nouvel objet `$forgotPassword`et l'insérer dans le champs password.

La fonction `resetForgotPassword` va servir quant à elle à reinitialiser le mot de passe depuis le backend vers la BDD. Le nouveau mot de passe va être récupéré et passer les contraintes de validation afin de terminer sa  validation. Puis le `user` faisant cette demande de nouveau mot de passe va être de nouveau authentifier via le `token` reçu dans la requête. Enfin si le `user` en question existe bien et si le `token` de vérification correspond bien au token de mot de passe oublié du `user` alors on peut encoder le nouveau mot de passe et l'insérer en base de donnée. De plus on attribut un nouveau `token` de verification au `user`

```php

    
    /**
     * @param $password
     * @return ForgotPassword
     */
    public function createNewForgotPassword($password): ForgotPassword
    {
        $forgotPassword = new ForgotPassword();
        $forgotPassword->setPassword($password['password']);
        return $forgotPassword;
    }

    public function resetForgotPassword(
        string $token,
        Request $request,
        UserRepository $repository,
        ValidatorInterface $validator
    )
    {
        //recuperation du nouveau mot de passe envoyé depuis react
        $password = json_decode($request->getContent(), true);

        //creation d'un objet forgotPassword et insertion du nouveau password react dans le champ password de l'objet
        $forgotPassword = $this->createNewForgotPassword($password);

        $passwordError = $validator->validateProperty($forgotPassword, 'password');
        $formErrors = $this->getFormError($passwordError);

        if ($formErrors) {
            return new Response($formErrors['passwordError']);
        }

        //recuperation de l'utilisateur lié au token et à la demande de mot de passe oublié
        $user = $repository->findOneBy(['forgotPasswordToken' => $token]);

        if ($user === null || $token !== $user->getForgotPasswordToken()) {
            return new Response("user not identified or token not equal to forgotPasswordToken", 400);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
        $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
        $this->entityManager->flush();
        return new Response("OK");

    }
```

---
## ContactController
Ce controller sert à la fonction d'envoi de message depuis le formulaire de contact. On y retrouvera la fonction `SendMessage`.

### SendMessage
La fonction `SendMessage` sert à créer l'objet contact, puis récupèrer les valeurs json et les insérer dans l'objet contact qu'on vient de créer, ce qui va permettre de recevoir un tableau avec l'objet json. Ensuite, elle permet aussi de récupérer les champs de l'entité crée `Contact.php`, et puis gérer les erreurs de chaque champs en utilisant `symfony validator`. Si le formulaire de contact ne contient pas d'erreur, le message sera envoyé grâce au `Mailer de Symfony`.

```php
public function SendMessage(Request $request, ValidatorInterface $validator)
    { 
       //Creation de l'objet
        $contact = new Contact();

       //Récuperer les valeurs json et les insérer dans le new contact que je viens de créer
       //je reçoi un tableau avec l'objet json
        $data =json_decode($request -> getContent(), true);
        //Récupérer les champs
        //En créant l'entité contact je vais devoir faire : 
        $contact -> setName($data['name']);
        $contact-> setEmail($data['email']);
        $contact-> setSubject($data['subject']);
        $contact-> setMessage($data['message']);

        //we need to customize the errors with symfony validator; 
        $nameError = $validator->validateProperty($contact, 'name');
        $emailError = $validator->validateProperty($contact, 'email');
        $subjectError = $validator->validateProperty($contact, 'subject');
        $messageError = $validator->validateProperty($contact, 'message');

        $formErrors = [];
        if(count($nameError) > 0) {
            $formErrors['nameError'] = $nameError[0]->getMessage();
        }
        if(count($emailError) > 0) {
            $formErrors['emailError'] = $emailError[0]->getMessage();
        }
        if(count($subjectError) > 0) {
            $formErrors['subjectError'] = $subjectError[0]->getMessage();
        }   
        if(count($messageError) > 0) {
            $formErrors['messageError'] = $messageError[0]->getMessage();
        }         
        if($formErrors) {
            return new JsonResponse($formErrors);
        }

        //Sending mail if the contact form does not contain errors
        if (!$formErrors)
        {
            $this->mailer->sendContactMessage($contact);
            return new Response('OK');
        }
    }
```

---
## AccountActivatorController
Recoit le token transmis par l'utilisateur quand celui ci clique sur le lien dans l'email qu'on a envoyé et active le compte de l'utilisateur puis redirige sur "home".

### ConfirmUser
La fonction `confirmUser` sert à recevoir le `token` présent dans le lien d'activation envoyé à l'utilisateur lors du processus d'activation d'un nouveau compte utilisateur. La fonction va faire appel à la class `UserConfirmationService` pour faire ce travail. Cette class va récupérer le `user` grâce au `token` et si le `user` est valide, elle va modifier la variable `isEnabledAccount` en BDD pour la mettre à `true`. Puis le `token` sera renouvelé. 

```php
public function confirmUser(string $tokenConfirmation)
    {
        $user = $this->repository->findOneBy(['tokenConfirmation' => $tokenConfirmation]);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabledAccount(true);
        $user->setTokenConfirmation(null);
        $this->entityManager->flush();
    }
```

---
## RegisterController

Ce controller va servir à la création d'un nouvel utilisateur grâce à `registerSubscriber` qui un doctrine subscriber, à la validation des champs renseigné dans le formulaire, encoder le mot de passe, créer le token et envoyer le mail de la mail de l'activation.

```php
    public function register(Request $request)
    {
        //on récupère le contenu de l'objet JSON
        $content = json_decode($request->getContent(), true);

        //Creation d'un nouvel utilisateur
        $user = new User();

        //on crée un formulaire symfony d'enregistrement de user tel que cela a été
        //défini dans la classe Usertype du package form
        //$form = $this->createForm(UserType::class, $user);
        $form = $this->createFrom(UserType::class, $user);
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
            //return $this->json($errors, 400);
        }

        //Ici intervient le registerSubscriber qui est un doctrine subscriber
        //et qui va encoder le mot de passe, créé le token pour envoyer la confirmation
        //et envoyer l'email pour l'activation du compte

        //sinon on fait persister le nouveau user créé
        $this->entityManager->persist($user);
        //puis on l'enregistre en BDD
        $this->entityManager->flush();

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
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
```

---
## ResetPasswordAction

### invoke
Ce controller avec cette unique fonction va servir à l'utilisateur pour changer son mot de passe depuis son compte. Ainsi, son mot de passe va passer dans le `validator` de **Symfony** afin de checker les contraintes de validations. Puis le nouveau mot de passe est encodé, le champ `$passwordChangeDate` est aussi modifié avec la date actuelle afin de garder une trace du dernier changement de mot de passe. Une fois ça fait, on enregistre en BDD puis on demande au bundle `Lexik JWT` de céer un nouveau JWT `token` et de l'envoyer dans la réponse transmise à l'utilisateur.

```php
public function __invoke(User $data)
    {
        //validation des données reçu 
        //au travers des contraintes 
        //de validation Symfony
        $this->validator->validate($data);

        //encodage du mot de passe
        $data->setPassword(
            $this->encoder->encodePassword(
                $data,
                $data->getNewPassword()
            )
        );

        //mise en place d'un traçage du dernier 
        // de mot de passe
        $data->setPasswordChangeDate(time());

        $this->entityManager->flush();

        //creation du nouveau JWT Token pour l'utilisateur
        $token = $this->JWTTokenManager->create($data);
        return new JsonResponse(['token' => $token]);

    }
```
