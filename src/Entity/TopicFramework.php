<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TopicFrameworkRepository")
 */
class TopicFramework extends Topic
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Framework", inversedBy="topic", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "author:read", "level:read", "framework:write"})
     * @Assert\NotNull
     */
    private $framework;

    public function getFramework(): ?Framework
    {
        return $this->framework;
    }

    public function setFramework(Framework $framework): self
    {
        $this->framework = $framework;

        return $this;
    }
}
