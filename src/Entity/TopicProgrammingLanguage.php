<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\ItemOutput;

/**
 * @ApiResource(
 *     mercure=true,
 *     normalizationContext={"groups"={"programLang:read"}},
 *     denormalizationContext={"groups"={"programLang:write"}},
 *     collectionOperations={
 *        "get-select-items"={
 *        "method"="GET",
 *        "path"="/programs/getItems",
 *        "normalization_context"={"groups"={"programLang:get-select-items"}},
 *        "output"=ItemOutput::class
 *        },
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TopicProgrammingLanguageRepository")
 */
class TopicProgrammingLanguage extends Topic
{
  /**
   * @ORM\OneToOne(targetEntity="App\Entity\Program", inversedBy="topic", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   * @Groups({"program:read", "resource:read", "author:read", "level:read", "program:write", "programLang:read", "programLang:get-select-items"})
   * @Assert\NotNull
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
