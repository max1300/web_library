# Modification du serializer API platform

---

Il est parfois necessaire d'aller plus loin dans le service de serialization de Api plaform (**qui repose en grande partie sur le serializer Symfony**)

Dans notre cas, cette utilisation plus poussée du `serializer` sert dans la gestion des comptes `user`. Il est normal en effet que chaque `user` ait accès à ses informations personnelles et puisse en modifier une partie selon ses désirs. Il est donc normal de restreindre cet accès au `user` en question et aux `admin`. 
Pour ce faire, on va modifier dynamiquement le contexte de serialization/deserialization. Ainsi avant chaque serialization/deserialization impliquant une instance de `user`, le role de ce dernier sera vérifier afin d'utiliser le bon group de normalization/denormalization.

#### *UserAttributeNormalizer*
Va modifier le contexte selon les permissions utilisateur.

```php
public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = "user:get-owner";
        }

        return $this->passOn($object, $format, $context);
    }
```

#### *UserContextBuiler*
Va modifier le contexte dynamiquement.

```php
public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->builder->createFromRequest(
            $request, $normalization, $extractedAttributes
        );

        $resourceClass = $context['resource_class'] ?? null;

        if( User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->checker->isGranted(User::ROLE_ADMIN)) {
            $context['groups'][] = 'user:get-admin';
        }

        return $context;
    }
```