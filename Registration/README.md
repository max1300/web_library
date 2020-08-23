# Les formulaires d'inscriptions
--------

## La class User
--------
Peu importe la façon dont s'authentifieront les utilisateurs (formulaire de connexion ou Token) et où seront stockées leur données (base de données ou authentification unique), on créera toujours une class User, le moyen le plus simple pour réaliser cette action sera d'utiliser le MakerBundle.

On pourra utiliser la commande *php bin/console make:user* qui posera plusieurs questions afin de générer exactement ce dont on a besoin. Le plus important est sera le fichier *user.php*. La seule règle concernant la class User est qu'elle doit être implémentée. Si la classe User est une entité on pourra ajouter plus de champs avec la commande *php bin/console make:entity*. Il faudra penser à effectuer et exécuter une migration pour la nouelle entité.

En plus de la classe User on aura besoin d'un **user provider** cette pourra nous aider dans certains domaines tel que le rechargement des données utilisateur et d'autre fonctionnalités facultatives.
Lors de l'utilisattion de la commande *php bin/console make:user* un provider a été automatiquement configuré dans notre fichier *security.yaml* sous la clé provider. Notre classe User est une entité nous n'avons donc rien d'autre à faire si cela n'avait pas été le cas *php bin/console make:user* aurait créer une classe UserProvider que l'on aurait du terminer. Pour en savoir plus sur les providers: [cliquez-ici](https://symfony.com/doc/current/security/user_provider.html).

Nos utilisateurs ont besoin de mots de passe pour se connecter à l'application, on peut controler la manière dont les mots de passe sont encodés dans le fichier *security.yaml* la commande *php bin/console make:user* à préconfiguré cela pour nous. On pourra ensuite utiliser le **UserPasswordEncoderInterface** pour encoder le mot de passe avant d'enregistrer nos utilisateurs dans la base de données.

## Le formulaire
--------
**--------**

## Le controller
--------
**--------**

## La protection de l'application
--------
**--------**
