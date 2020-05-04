<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *      "get"={"path"="/ressource/{id}"},
 *      "put"={"path"="/ressource/{id}"},
 *      "delete"={"path"="/ressource/{id}"},
 *      "patch"={"path"="/ressource/{id}"}
 *     },
 *     collectionOperations={
 *      "post"={"path"="/ressource"},
 *      "get"={"path"="/ressources"}
 *     },
 *     normalizationContext={"groups"={"resource:read"}},
 *     denormalizationContext={"groups"={"resource:write"}}
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={
 *          "author": "exact",
 *          "author.name" : "partial",
 *          "level.name" : "partial"
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RessourceRepository")
 */
class Ressource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "resource:write", "author:read", "level:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write", "author:read", "level:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write", "author:read"})
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="ressources", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write"})
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write"})
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write", "author:read"})
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topic", inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "resource:write", "author:read", "level:read"})
     */
    private $topic;

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
}
