# Les eventListener

---

## EntityCreatedListener
Cette classe a pour vocation de remplir automatiquement le champ `createdAt` des entités implémentant l'interface `PublishedAtInterface`.

Ainsi lorsqu'un objet de cette entité est créé, le champ sera rempli avec la date de création de l'objet. 

---

## JWTListener
Le JWTListener va servir à rajouter des informations dans la réponse renvoyée lors d'une tentative de connexion. 
Dans notre cas, on envoit le login de l'utilisateur qui se connecte lors d'une tentative de connexion réussie. La réponse renvoyé lors d'une tentative de connexion réussie sera donc une réponse 200 avec un JWT token, qui permet d'authentifier l'utilisateur, et des `data` contenant le `login` 

```php
public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $successEvent)
    {
        $data = $successEvent->getData();
        $user = $successEvent->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = [
            'login' => $user->getLogin()
        ];

        $successEvent->setData($data);

    }
```