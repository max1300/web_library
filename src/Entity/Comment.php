<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\CommentOutput;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *     "get",
 *     "put"={
 *        "security"="is_granted('ROLE_ADMIN') or object.getUser() == user",
 *        "security_message"="Sorry, but only admins or owner of the account can modify this account."
 *      },
 *      "delete"={
 *        "security"="is_granted('ROLE_ADMIN')",
 *        "security_message"="Only admins can delete users."
 *      }
 *     },
 *     collectionOperations={
 *      "post"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *      },
 *      "get"
 *     },
 *     output=CommentOutput::class,
 *     normalizationContext={"groups"={"comment:read"}},
 *     denormalizationContext={"groups"={"comment:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"comment:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"comment:read", "comment:write"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"comment:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ressource", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment:read", "comment:write"})
     */
    private $ressource;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"comment:read"})
     */
    private $user;
    
    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRessource(): ?Ressource
    {
        return $this->ressource;
    }

    public function setRessource(?Ressource $ressource): self
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
