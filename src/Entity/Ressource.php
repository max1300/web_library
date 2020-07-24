<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\RessourceOutput;
use DateTimeInterface;


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
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write", "comment:read"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write"})
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="ressources", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write"})
     * @Assert\NotBlank
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write"})
     * @Assert\Choice({"French", "English"})
     */
    protected $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write"})
     * @Assert\NotBlank
     * @Assert\Valid()
     * @ApiSubresource(maxDepth=1)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write"})
     * @Assert\NotBlank
     * @Assert\Valid()
     */
    private $topic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ressource", orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read"})
     */
    private $user;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRessource($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getRessource() === $this) {
                $comment->setRessource(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): PublishedAtInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): AuthorEntityInterface
    {
        $this->user = $user;

        return $this;
    }
}
