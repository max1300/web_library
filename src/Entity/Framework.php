<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *     "get"={"path"="/framework/{id}"},
 *      "put"={"path"="/framework/{id}"},
 *      "delete"={"path"="/framework/{id}"},
 *      "patch"={"path"="/framework/{id}"}
 *     },
 *     collectionOperations={
 *      "post"={"path"="/framework"},
 *      "get"={"path"="/frameworks"}
 *     },
 *     denormalizationContext={"groups"={"framework:write"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\FrameworkRepository")
 */
class Framework
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"resource:read", "author:read", "level:read", "framework:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "author:read", "level:read", "framework:write"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Program", inversedBy="frameworks")
     * @Groups({"resource:read", "author:read", "level:read"})
     * @Assert\NotNull
     */
    private $program;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"resource:read", "author:read", "framework:write"})
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $docUrl;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TopicFramework", mappedBy="framework", cascade={"persist", "remove"})
     * @Groups("framework:write")
     * @Assert\NotBlank
     */
    private $topic;

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

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getDocUrl(): ?string
    {
        return $this->docUrl;
    }

    public function setDocUrl(string $docUrl): self
    {
        $this->docUrl = $docUrl;

        return $this;
    }

    public function getTopic(): ?TopicFramework
    {
        return $this->topic;
    }

    public function setTopic(TopicFramework $topic): self
    {
        $this->topic = $topic;

        // set the owning side of the relation if necessary
        if ($topic->getFramework() !== $this) {
            $topic->setFramework($this);
        }

        return $this;
    }
}
