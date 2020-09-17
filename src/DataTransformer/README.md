### Dans le but de la refacto :

- On a besoin d'un format "constant" dans le front
- On va donc adapter notre back pour éviter les retours du type '@id, framework: name: ...'
- On a donc besoin d'un DTO qui nous renverra les données sous la forme 'value, label' par exemple c'est-à-dire un format exploitable par une balise select.


`value = @id`
`libellé = name`

On veut avoir un format qui ressemble à :

```php
[{
  value: "/api/resource/5",
  label: "Mon libellé"
},
{
  value: "/api/resource/6",
  label: "Mon autre libellé"
},
...
]
```

A partir d'une entity on va vouloir mettre des chose dans `value` et `label`.

##### Pour la value : 

- Il va falloir remplacer l'ID qu'on envoit en tant que value par l'IRI
- Pour faire ça, on peut utiliser un service fourni par API Platform : IriConverterInterface
Ce service possède une méthode qui peut vous renvoyer l'IRI d'une entité

Donc quelque soit l’item on aura le moyen de recuper son iri grace à ça : 

```php
    $output->value = $this->iriConverter->getIriFromItem($data);
```

##### Pour le label : 

On veut faire une opération d’affectation commune, on veut mettre dans notre DTO `itemOutput` une valeur, mais selon le type d’entité.


On va définir une methode `getLabel` qui est un contrat d'implementation quelque soit le type de l'entité. On va faire du polymorphisme dans `$data->getLabel()`, que ce soit un level, un author, un programme ou un framework.
le `ItemOutputDataTransformer.js` saura que le contrat est respecté par l'entité.
Pour cela, on va devoir dans notre dossier entity créer un nouveau fichier : `IItemOutputTransformable.php`, qui est une interface.

-> Interface permet de définir des comportements pour les classes 
-> On sait donc que si une classe implémente une interface alors elle redéfinie obligatoirement les méthodes de l'interfaces.
-> Une interface permet donc de généraliser des "comportements" pour des classe.
une interface ne peut contenir que des méthodes abstraites.
-> Donc des méthodes qui n'ont pas de corps, juste la signature.

```php

<?php

namespace App\Entity;

//Interface préfixé par "I"

//Définir une interface

interface IItemOutputTransformable 
{
    //on va définir que toute classe qui vont implementer cette interface 
    //va devoir implementer la methode definit dans l interface
    public function getLabel():string;

}

```

De cette façon, nos entités pourront implémenter cette interface et sa méthode.
A titre d'exemple dans l'entité `Level.php`, on aura juste à rajouter :
class Level `implements IItemOutputTransformable`, comme ça elle implémentera sa méthode :

```php
    public function getLabel(): string
    {
        return $this->getName();
    }
```

Dans l'entité `TopicFramework.php` : 

```php
    public function getLabel(): string
    {
        return $this->getFramework()->getName();
    }
```

Et par conséquent, dans notre data transformer ItemOutputDataTransformer, on aura juste à utiliser : 

```php
    $output->label = $data->getLabel();
```
pour récupérer les labels de chaque entité.



#### Useful infos : 

- On peut pas faire d’héritage multiple en php.
- topicProgrammng language est un héritage de topic.
- L’heritage veut dire qu’on peut pas faire une autre classe.
- On peut pas deviner à une execution le type de l’entité.
- Dans le cas où on a un même namespace, pas besoin du use.
- Dans le cas où on a un même namespace, pas besoin du use.

