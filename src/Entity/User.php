<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\UserOutput;
use App\Controller\ResetPasswordAction;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EntityFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     mercure=true,
 *     itemOperations={
 *     "get"={
 *          "acces_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *          "normalization_context"={"groups"={"user:get"}}
 *      },
 *      "put"={
 *        "security"="is_granted('ROLE_ADMIN') or object == user",
 *        "security_message"="Sorry, but only admins or owner of the account can modify this account.",
 *         "denormalization_context"={"groups"={"user:put"}},
 *         "normalization_context"={"groups"={"user:get"}}
 *      },
 *      "put-reset-password"={
 *        "security"="is_granted('ROLE_ADMIN') or object == user",
 *        "security_message"="Sorry, but only admins or owner of the account can modify this account.",
 *        "method"="PUT",
 *        "path"="/users/{id}/reset-password",
 *        "route_name"="reset-password",
 *        "controller"=ResetPasswordAction::class,
 *        "denormalization_context"={"groups"={"user:put-reset-password"}}
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
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:post", "user:get-admin", "user:get-owner"})
     * @Assert\NotBlank(groups={"user:post"})
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     groups={"user:post"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:get-admin", "user:get-owner"})
     */
    private $roles = [];


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:post", "user:put"})
     * @Assert\NotBlank(groups={"user:post", "user:put"})
     * @Assert\Regex(
     *     pattern="/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
     *     message="Password must be at least seven character long and containe at least one digit or one special character, one upper case letter and one lower case letter",
     *     groups={"user:post", "user:put"}
     * )
     */
    private $password;

    /**
     * @var string The hashed password
     * @Groups({"user:put-reset-password"})
     * @Assert\NotBlank(groups={"user:put-reset-password"})
     * @Assert\Regex(
     *     pattern="/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
     *     message="Password must be at least seven character long and containe at least one digit or one special character, one upper case letter and one lower case letter",
     *     groups={"user:put-reset-password"}
     * )
     */
    private $newPassword;

    /**
     * @Groups({"user:put-reset-password"})
     * @Assert\NotBlank(groups={"user:put-reset-password"})
     * @UserPassword(groups={"user:put-reset-password"})
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:get", "user:post", "user:put", "resource:read"})
     * @Assert\NotBlank(groups={"user:post", "user:put"})
     * @Assert\Length(min=5, max=255, groups={"user:post", "user:put"})
     */
    private $login;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="profilePic", size="profilePicSize")
     * 
     * @var File|null
     */
    private $profilePicFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:get", "user:post", "user:put"})
     * @var String|null
     */
    private $profilePic;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var Int|null
     */
    private $profilePicSize;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     * @ApiSubresource(maxDepth=1)
     */
    private $comments;
  
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $tokenConfirmation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $forgotPasswordToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabledAccount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ressource", mappedBy="user")
     * @ApiSubresource(maxDepth=1)
     */
    private $ressources;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->ressources = new ArrayCollection();
        $this->enabledAccount = false;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
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

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param EntityFile|null $profilePicFile
     * @throws Exception
     */
    public function setProfilePicFile(?EntityFile $profilePicFile = null): void
    {
        $this->profilePicFile = $profilePicFile;

        if (null !== $profilePicFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getProfilePicFile(): ?EntityFile
    {
        return $this->profilePicFile;
    }

    public function setProfilePic(?string $profilePic): self
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profilePic;
    }

    public function setProfilePicSize (? int  $profilePicSize ): void
    {
        $this->profilePicSize = $profilePicSize;
    }

    public function getProfilePicSize () :? int
    {
        return  $this->profilePicSize;
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

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }


    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }


    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }



    public function isEnabledAccount(): bool
    {
        return $this->enabledAccount;
    }


    public function setEnabledAccount(bool $enabledAccount): void
    {
        $this->enabledAccount = $enabledAccount;
    }

    /**
     * @return mixed
     */
    public function getTokenConfirmation()
    {
        return $this->tokenConfirmation;
    }

    /**
     * @param mixed $tokenConfirmation
     */
    public function setTokenConfirmation($tokenConfirmation): void
    {
        $this->tokenConfirmation = $tokenConfirmation;
    }

    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    public function setForgotPasswordToken(string $forgotPasswordToken): self
    {
        $this->forgotPasswordToken = $forgotPasswordToken;

        return $this;
    }
}
