<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProgramRepository")
 */
class Program
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Framework", mappedBy="program")
     */
    private $frameworks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="program", orphanRemoval=true)
     */
    private $ressources;

    public function __construct()
    {
        $this->frameworks = new ArrayCollection();
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
     * @return Collection|Framework[]
     */
    public function getFrameworks(): Collection
    {
        return $this->frameworks;
    }

    public function addFramework(Framework $framework): self
    {
        if (!$this->frameworks->contains($framework)) {
            $this->frameworks[] = $framework;
            $framework->setProgram($this);
        }

        return $this;
    }

    public function removeFramework(Framework $framework): self
    {
        if ($this->frameworks->contains($framework)) {
            $this->frameworks->removeElement($framework);
            // set the owning side to null (unless already changed)
            if ($framework->getProgram() === $this) {
                $framework->setProgram(null);
            }
        }

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
            $ressource->setProgram($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getProgram() === $this) {
                $ressource->setProgram(null);
            }
        }

        return $this;
    }
}
