<?php

namespace App\Entity;

use App\Repository\ChangelogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChangelogRepository::class)]
class Changelog extends DataEntity
{
    public const TYPE_INFO = 0;
    public const TYPE_WARNING = 1;
    public const TYPE_DANGER = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:read'])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private $type = self::TYPE_INFO;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:read'])]
    private $isPublished = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:read'])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['user:read'])]
    private $createdAt;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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
    #[Groups(['user:read'])]
    public function getTypeString(): ?string
    {
        $values = ["Information", "Attention", "Danger"];

        return $values[$this->type];
    }


    /**
     * @return string|null
     */
    #[Groups(['user:read'])]
    public function getTypeIcon(): ?string
    {
        $values = ["exclamation", "warning", "warning"];

        return $values[$this->type];
    }

    /**
     * @return string|null
     */
    #[Groups(['user:read'])]
    public function getCreatedAtString(): ?string
    {
        return $this->getFullDateString($this->createdAt, 'llll');
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }
}
