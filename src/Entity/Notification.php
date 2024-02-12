<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification extends DataEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['admin_read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin_read'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin_read'])]
    #[Assert\NotBlank]
    private ?string $icon = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['admin_read'])]
    #[Assert\NotBlank]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['admin_read'])]
    private ?bool $isSeen = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['admin_read'])]
    private ?string $url = null;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->isSeen = false;
    }

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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[Groups(['admin_read'])]
    public function getCreatedAtAgo(): ?string
    {
        return $this->getHowLongAgo($this->createdAt);
    }

    public function getIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): self
    {
        $this->isSeen = $isSeen;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function isIsSeen(): ?bool
    {
        return $this->isSeen;
    }
}
