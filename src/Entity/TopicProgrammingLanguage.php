<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\ItemOutput;
use App\Dto\ProgramingLanguageOutput;

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
 *        "post",
 *        "get"
 *     },
 *     output=ProgramingLanguageOutput::class,
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TopicProgrammingLanguageRepository")
 */
class TopicProgrammingLanguage extends Topic implements IItemOutputTransformable
{
  /**
   * @ORM\OneToOne(targetEntity="App\Entity\Program", inversedBy="topic", cascade={"persist", "remove"})
   * @ORM\JoinColumn(nullable=false)
   * @Groups({"program:read", "resource:read", "author:read", "level:read", "program:write", "programLang:write", "programLang:read", "programLang:get-select-items"})
   */
  private $programmingLanguage;

  public function getLabel(): string
  {
    return $this->getProgrammingLanguage()->getName();
  }

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
