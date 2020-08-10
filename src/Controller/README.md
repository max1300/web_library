# Les controller
---

## AuthController
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
### *mot de passe oublié*
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

La fonction `resetForgotPassword` va servir quant à elle à reinitialiser le mot de passe depuis le backend vers la BDD. Le nouveau mot de passe va être récupéré et passer les contraintes de validation afin de terminer sa  validation. Puis le `user` faisant cette demande de nouveau mot de passe va être de nouveau authentifier via le `token` reçu dans la requête. Enfin si le `user` en question existe bien et si le `token` de vérification correspond bien au token de mot de passe oublié du `user` alors on peut encoder le nouveau mot de passe et l'insérer en base de donnée. De plus on attribut un nouveau `token` de verification au `user`

```php
/**
     * @Route("/reset-forgot-password/{token}", name="reset-forgot-password", methods={"PUT"})
     * @param string $token
     * @param Request $request
     * @param UserRepository $repository
     * @param ValidatorInterface $validator
     * @return Response
     */
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

        if ($user !== null && $token === $user->getForgotPasswordToken())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
            $this->entityManager->flush();
            return new Response("OK");
        }
    }
```

---
## ContactController


---
## DefaultController

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
