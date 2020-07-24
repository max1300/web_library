# Les DTO
![Image of startocat](https://images-na.ssl-images-amazon.com/images/I/41tMI1Rv75L._AC_.jpg)

L'application back de symfony dispose de _DTO_. (**Documentation sur les *Data Transfer Object*** = [dto](https://fr.wikipedia.org/wiki/Objet_de_transfert_de_donn%C3%A9es)))

Dans le cas de l'application les _DTO_ servent à représenter la **structure des données** en lecture. C'est pourquoi Le nom de chaque classe de DTO peut être divisée en deux :
1. Le nom de la classe _(exemple : user)_
2. **_Output_** pour expliquer que ce sont des données en sortie et donc en lecture et donc serialisées.

Ce qui aura pour résultat **UserOutput**.

***

## Conception
1. Pour commencer à utiliser les DTO avec Api Platform, il faut donc déjà créer une classe. On initialisera les attributs qu'on souhaite voir apparaître.
On oubliera pas d'attribuer à chaque attribut les groups de sérialisation souhaités

```sh
final class RessourceOutput
{

    /**
     * @var string
     * @Groups({"program:read", "resource:read", "level:read", "comment:read", "framework:read"})
     */
    public $resourceName;

    /**
     * @var string
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $url;

    /**
     * @var Author
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $author;

    /**
     * @var Level
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $level;

    /**
     * @var string
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $language;

    /**
     * @var Topic
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $topic;

    /**
     * @var User
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $publisher;


}

```
2.Ensuite il va falloir créer un *dataTransformer*. Il s'agît en fait d'une classe qui va permettre de transformer les attibuts de l'entitée PHP en variables du *DTO*. 
```sh
class RessourceOutputDataTransformer implements DataTransformerInterface
{

    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Transforms the given object to something else, usually another object.
     * This must return the original object if no transformations have been done.
     *
     * @param $data
     * @param string $to
     * @param array $context
     * @return RessourceOutput
     */
    public function transform($data, string $to, array $context = []): RessourceOutput
    {
        $this->validator->validate($data);

        $output = new RessourceOutput();
        $output->resourceName = $data->getName();
        $output->url = $data->getUrl();
        $output->author = $data->getAuthor();
        $output->level = $data->getLevel();
        $output->language = $data->getLanguage();
        $output->topic = $data->getTopic();
        $output->publisher = $data->getUser();
        return $output;
    }
    
    /**
     * Checks whether the transformation is supported for a given data and context.
     *
     * @param object|array $data object on normalize / array on denormalize
     * @param string $to
     * @param array $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return RessourceOutput::class === $to && $data instanceof Ressource;
    }
}

````

3.Il ne faut pas oublier de préciser à l'entité à quel *DTO* elle se rapporte.
```sh
// Ne pa soublier le use dans l entité ressource
use App\Dto\RessourceOutput;
```
```sh
/**
 * @ApiResource(
 *     attributes={
 *       "security"="is_granted('ROLE_USER')",
 *       "order"={"createdAt": "DESC"}
 *     },
 *     mercure=true,
 *     itemOperations={
 *      "get",
 *      "put"={
 *        "security"="is_granted('ROLE_ADMIN') or object.user == user",
 *        "security_message"="Sorry, but only admins or publisher of the ressources can modify them."
 *      },
 *      "delete"={
 *        "security"="is_granted('ROLE_ADMIN')",
 *        "security_message"="Only admins can delete ressources."
 *      },
 *      "patch"
 *     },
 *     collectionOperations={
 *      "post"={
 *        "acces_control"="is_granted('ROLE_ADMIN') or object.user == user"
 *      },
 *      "get"
 *     },
 *      // on déclare ici le nom du DTO qui se raaporte à l entité
 *     output=RessourceOutput::class,
 *     normalizationContext={"groups"={"resource:read"}},
 *     denormalizationContext={"groups"={"resource:write"}},
 *     attributes={"order"={"author.name"}}
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={
 *          "topic": "exact"
 *     }
 *     
 * )
 * @ApiFilter(OrderFilter::class, properties={"createdAt"="desc", "topic"="exact"})
 * @ORM\Entity(repositoryClass="App\Repository\RessourceRepository")
 */
class Ressource implements AuthorEntityInterface, PublishedAtInterface
{
}
```



