<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\AuthorOutput;
use App\Dto\ItemOutput;


/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *      "get",
 *      "put",
 *      "delete",
 *     },
 *     collectionOperations={
 *       "post",
 *       "get",
 *       "get-select-items"={
 *       "method"="GET",
 *       "path"="/authors/getItems",
 *       "normalization_context"={"groups"={"authors:get-select-items"}},
 *       "output"=ItemOutput::class
 *      },
 *     },
 *     output=AuthorOutput::class,
 *     normalizationContext={"groups"={"author:read"}},
 *     denormalizationContext={"groups"={"author:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "resource:write", "author:read", "author:write", "authors:get-select-items"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:write", "author:read", "author:write", "authors:get-select-items"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"resource:write", "author:read", "author:write", "authors:get-select-items"})
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="author", orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     * @ApiProperty(push=true)
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