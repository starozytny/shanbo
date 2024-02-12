<?php

namespace App\Entity;

use App\Repository\InformRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InformRepository::class)]
class Inform extends DataEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['admin:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['admin:read'])]
    private $email;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
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
     * @return string|null
     */
    #[Groups(['admin:read'])]
    public function getCreatedAtAgo(): ?string
    {
        return $this->getHowLongAgo($this->createdAt);
    }
}
