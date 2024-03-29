<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'])]
#[UniqueEntity(fields: ['email'])]
class User extends DataEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const FOLDER_AVATARS = "avatars";

    public const ADMIN_READ = ['admin_read'];
    public const USER_READ = ['user_read'];
    public const VISITOR_READ = ['visitor:read'];

    public const CODE_ROLE_USER = 0;
    public const CODE_ROLE_DEVELOPER = 1;
    public const CODE_ROLE_ADMIN = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['admin_read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'alnum')]
    #[Groups(['admin_read', 'user_read'])]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['admin_read', 'user_read'])]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['admin_read'])]
    private ?array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin_read', 'user_read'])]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin_read', 'user_read'])]
    private ?string $firstname = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $forgetCode = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $forgetAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin_read'])]
    private ?string $token = null;

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column(type: 'string')]
    #[Groups(['admin:write'])]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['admin_read', 'user_read'])]
    private ?string $avatar = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Album::class, orphanRemoval: true)]
    private Collection $albums;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->token = $this->initToken();
        $this->notifications = new ArrayCollection();
        $this->albums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    #[Groups(['admin_read'])]
    public function getHighRole(): string
    {
        $rolesSortedByImportance = ['ROLE_DEVELOPER', 'ROLE_ADMIN', 'ROLE_USER'];
        $rolesLabel = ['Développeur', 'Administrateur', 'Utilisateur'];
        $i = 0;
        foreach ($rolesSortedByImportance as $role)
        {
            if (in_array($role, $this->roles))
            {
                return $rolesLabel[$i];
            }
            $i++;
        }

        return "Utilisateur";
    }

    #[Groups(['admin_read'])]
    public function getHighRoleCode(): int
    {
        return match ($this->getHighRole()) {
            'Développeur' => self::CODE_ROLE_DEVELOPER,
            'Administrateur' => self::CODE_ROLE_ADMIN,
            default => self::CODE_ROLE_USER,
        };
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
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
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    #[Groups(['admin_read'])]
    public function getLastLoginAgo(): ?string
    {
        return $this->getHowLongAgo($this->getLastLogin());
    }

    public function getForgetCode(): ?string
    {
        return $this->forgetCode;
    }

    public function setForgetCode(?string $forgetCode): self
    {
        $this->forgetCode = $forgetCode;

        return $this;
    }

    public function getForgetAt(): ?\DateTimeInterface
    {
        return $this->forgetAt;
    }

    public function setForgetAt(?\DateTimeInterface $forgetAt): self
    {
        $this->forgetAt = $forgetAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getHiddenEmail(): string
    {
        $email = $this->getEmail();
        $at = strpos((string) $email, "@");
        $domain = substr((string) $email, $at, strlen((string) $email));
        $firstLetter = substr((string) $email, 0, 1);
        $etoiles = "";
        for($i=1 ; $i < $at ; $i++){
            $etoiles .= "*";
        }
        return $firstLetter . $etoiles . $domain;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return string
     */
    #[Groups(['admin_read'])]
    public function getFullname(): string
    {
        return $this->getFullNameString($this->lastname, $this->firstname);
    }

    /**
     * @return string
     */
    #[Groups(['admin_read'])]
    public function getAvatarFile(): string
    {
        return $this->getFileOrDefault($this->avatar, self::FOLDER_AVATARS, "https://robohash.org/" . $this->username . "?size=64x64");
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums[] = $album;
            $album->setUser($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getUser() === $this) {
                $album->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['admin_read'])]
    public function getCreatedAtString(): ?string
    {
        return $this->getFullDateString($this->createdAt);
    }

    #[Groups(['admin_read'])]
    public function getCreatedAtAgo(): string
    {
        return $this->getHowLongAgo($this->getCreatedAt());
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
