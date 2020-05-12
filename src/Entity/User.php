<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\UserOutput;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *     "get"={
 *          "normalization_context"={"groups"={"user:get"}}
 *      },
 *      "put"={
 *        "security"="is_granted('ROLE_ADMIN') or object == user",
 *        "security_message"="Sorry, but only admins or owner of the account can modify this account.",
 *         "denormalization_context"={"groups"={"user:put"}}
 *      },
 *      "delete"={
 *        "security"="is_granted('ROLE_ADMIN')",
 *        "security_message"="Only admins can delete users."
 *      }
 *     },
 *     collectionOperations={
 *      "post"={
 *          "denormalization_context"={"groups"={"user:post"}}
 *       },
 *      "get"={
 *          "normalization_context"={"groups"={"user:get"}}
 *      }
 *     },
 *     output=UserOutput::class
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:post"})
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:post", "user:put"})
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
     *     message="Password must be at least seven character long and containe at least one digit or one special character, one upper case letter and one lower case letter"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:get", "user:post", "user:put"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=255)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:get", "user:post", "user:put"})
     */
    private $profilPic;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     * @Groups({"user:get"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="user")
     */
    private $ressources;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->ressources = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getProfilPic(): ?string
    {
        return $this->profilPic;
    }

    public function setProfilPic(?string $profilPic): self
    {
        $this->profilPic = $profilPic;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
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
            $ressource->setUser($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->contains($ressource)) {
            $this->ressources->removeElement($ressource);
            // set the owning side to null (unless already changed)
            if ($ressource->getUser() === $this) {
                $ressource->setUser(null);
            }
        }

        return $this;
    }
}
