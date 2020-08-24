# Les classes liées à la sécurité
------

## CustomTokenAuthenticator
Cette classe sert avant tout gérer le cas dans lequel un utilisateur modifirait son mot de passe depuis sa page de compte. 
Donc si un utilisateur modifie son mot de passe, cette classe va faire en sorte de s'assurer que lors d'un login, la date de création du JWT token soit toujours postérieur à la dernière date de changement du mot de passe utilisateur. Si ce n'est pas le cas alors on lance une exception. 

```php
if ($user->getPasswordChangeDate() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate())
    {
        throw new ExpiredTokenException();
    }
```

---

## EnabledAccountChecker

Cette classe sert essentiellement à s'assurer qu'a chaque login d'un utilisateur son compte à bien été activé. Cela se fait simplement en allant vérifier la variable boolean ```$enabledAccount``` située dans l'entité **User**.

```php
/**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        if(!$user instanceof User) {
            return;
        }

        if(!$user->isEnabledAccount()) {
            throw new DisabledException();
        }
    }

```
---

## TokenGenerator
Cette classe s'occupe de générer un token. Ce token pourra être utilisé pour différentes taches comme la réinitialisation de mot de passe par exemple. 

Exemple d'utilisation dans le AuthController lors du login lorsqu'un utilisateur a oublié son mot de passe. 
```php
if ($user !== null && $token === $user->getForgotPasswordToken())
        {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $forgotPassword->getPassword()));
            $user->setForgotPasswordToken($this->tokenGenerator->getRandomToken());
            $this->entityManager->flush();
            return new Response("OK");
        }
```

---

## UserConfirmationService
(**voir README.md dans le package EventSubscriber**)


