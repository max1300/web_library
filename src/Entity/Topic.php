<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TopicRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *   "framework" = "TopicFramework",
 *   "programmingLanguage" = "TopicProgrammingLanguage"
 * })
 */
abstract class Topic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="topic")
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
            $ressource->setTopic($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getTopic() === $this) {
                $ressource->setTopic(null);
            }
        }

        return $this;
    }
}