<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={"path"="/level/{id}"},
 *      "put"={"path"="/level/{id}"},
 *      "delete"={"path"="/level/{id}"},
 *      "patch"={"path"="/level/{id}"}
 *     },
 *     collectionOperations={
 *      "post"={"path"="/level"},
 *      "get"={"path"="/levels"}
 *     },
 *     normalizationContext={"groups"={"level:read"}},
 *     denormalizationContext={"groups"={"level:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "level:read", "level:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "level:read", "level:write"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="level", orphanRemoval=true)
     * @Groups("level:read")
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
            $ressource->setLevel($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getLevel() === $this) {
                $ressource->setLevel(null);
            }
        }

        return $this;
    }
}