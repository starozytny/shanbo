<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact extends DataEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['admin:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin:read'])]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin:read'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    #[ORM\Column(type: 'text')]
    #[Groups(['admin:read'])]
    #[Assert\NotBlank]
    private $message;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['admin:read'])]
    private $isSeen = false;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * How long ago a user was added.
     *
     * @return string
     */
    #[Groups(['admin:read'])]
    public function getCreatedAtAgo(): string
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

    public function isIsSeen(): ?bool
    {
        return $this->isSeen;
    }
}
