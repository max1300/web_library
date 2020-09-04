***dans l'affichage des programs, on ne part plus de "api/programs" mais de "api/topic_programming_languages"***

**ProgramingLanguageOutput.php** -> creation d'un DTO pour topic_programming_language
$idTopicProgramming = l'id du topic_programming_language
$id = l'id de l'entity Program
$programName = le nom du program
$frameworks = la liste des frameworks liés au program
$ressources = la liste des ressources liées au Program

**ProgrammingLanguageOutputDataTransformer.php** -> le dataTransformer lié à ProgramingLanguageOutput.php
idTopicProgramming = directement prélevé sur l'entity TopicProgrammingLanguage.php
id = l'id du program (on y accède grace à la relation vers Program.php contenue dans TopicProgrammingLanguage.php sous le nom de getProgrammingLanguage(), ce qui nous permet de faire un getId() sur l'entity Program.php)
programName = le name du program (la meme chose que pour id mais on accède au nom via le getter getName())
frameworks = la liste des frameworks liés à un Program.php (contenue dans Program.php on y accède de la même manière que pour id et programName et on récupère la liste via le getter getFrameworks())
ressources = la liste des ressources d'un program, elle est disponible via Topic.php (on y accède facilement car TopicProgrammingLanguage.php est une classe fille de Topic.php, elles sont donc directement liées par l'id et on peut accéder au getter de Topic.php getRessources())

**FrameworkOutput.php** -> on y ajoute le group de serialization de topic_programming_language = "programLang:read"
ce group est placé sur $frameworkName, $docUrl

**RessourceOutput.php** -> comme pour FrameworkOutput, ce group est placé sur $id, $resourceName, $url, $author, $level, $language, $publisher

**Program.php** -> on ajoute 's' à program pour tous les itemsOperations

itemOperations={
 *     "get"={"path"="/programs/{id}"},
 *      "put"={"path"="/programs/{id}"},
 *      "delete"={"path"="/programs/{id}"},
 *      "patch"={"path"="/programs/{id}"}
 *     }

**Topic.php** -> ajout de l'annotation 'cascade={remove}' pour supprimer les ressources liées à un program lorsque ce program est supprimé

**TopicProgrammingLanguage.php** -> ajout de l'annotation 'output=ProgramingLanguageOutput::class' afin d'affilier le DTO et l'entité.
Le group de serialization ```php "programLang:read" ``` permet de transformer en objet JSON l'objet php Topic_programming_language. Les champs annotés par ce group
seront alors inclus dans l'objet JSON. Ces champs peuvent provenir de l'entity Topic_programming_language mais aussi d'autres entity

exemple dans le FrameworkOutput.php:
```php
/**
     * @var string
     * @Groups({"resource:read", "author:read", "framework:write", "framework:read", "program:read", "programLang:read"})
     */
    public $docUrl;
```
