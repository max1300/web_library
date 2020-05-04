<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *      "get"={"path"="/author/{id}"},
 *      "put"={"path"="/author/{id}"},
 *      "delete"={"path"="/author/{id}"},
 *      "patch"={"path"="/author/{id}"}
 *     },
 *     collectionOperations={
 *      "post"={"path"="/author"},
 *      "get"={"path"="/authors"}
 *     },
 *     normalizationContext={"groups"={"author:read"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "resource:write", "author:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "resource:write", "author:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"resource:read", "resource:write", "author:read"})
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="author", orphanRemoval=true)
     * @Groups("author:read")
     */
    private $ressources;

    public function __construct()
    {
        $this->ressources = new ArrayCollection();
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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources[] = $ressource;
            $ressource->setAuthor($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getAuthor() === $this) {
                $ressource->setAuthor(null);
            }
        }

        return $this;
    }
}