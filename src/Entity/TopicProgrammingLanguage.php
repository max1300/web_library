<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(mercure=true)
 * @ORM\Entity(repositoryClass="App\Repository\TopicProgrammingLanguageRepository")
 */
class TopicProgrammingLanguage extends Topic
{
  /**
   * @ORM\OneToOne(targetEntity="App\Entity\Program", inversedBy="topic", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   * @Groups({"resource:read", "author:read", "level:read", "program:write"})
   */
  private $programmingLanguage;

  public function getProgrammingLanguage(): ?Program
  {
    return $this->programmingLanguage;
  }

  public function setProgrammingLanguage(Program $programmingLanguage): self
  {
    $this->programmingLanguage = $programmingLanguage;

    return $this;
  }
}
