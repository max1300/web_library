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
 *     normalizationContext={"groups"={"topicFram:write"}},
 *     collectionOperations={
 *        "get-select-items"={
 *        "method"="GET",
 *        "path"="/frameworks/getItems",
 *        "normalization_context"={"groups"={"topicFram:get-select-items"}},
 *        "output"=ItemOutput::class
 *      },
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TopicFrameworkRepository")
 */
class TopicFramework extends Topic implements IItemOutputTransformable
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Framework", inversedBy="topic", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resource:read", "author:read", "level:read", "framework:write", "framework:read", "topicFram:get-select-items"})
     * @Assert\NotNull
     */
    private $framework;

    public function getLabel(): string
    {
        return $this->getFramework()->getName();
    }

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
