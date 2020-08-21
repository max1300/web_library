# Les formulaires d'inscriptions
--------

## La class User
--------
Peu importe la façon dont s'authentifieront les utilisateurs (formulaire de connexion ou Token) et où seront stockées leur données (base de données ou authentification unique), on créera toujours une class User, le moyen le plus simple pour réaliser cette action sera d'utiliser le MakerBundle.

On pourra utiliser la commande *php bin/console make:user* qui posera plusieurs questions afin de générer exactement ce dont on a besoin. Le plus important est sera le fichier *user.php*. La seule règle concernant la class User est qu'elle doit être implémentée. Si la classe User est une entité on pourra ajouter plus de champs avec la commande *php bin/console make:entity*. Il faudra penser à effectuer et exécuter une migration pour la nouelle entité.


## Le formulaire
--------
**--------**

## Le controller
--------
**--------**

## La protection de l'application
--------
**--------**
