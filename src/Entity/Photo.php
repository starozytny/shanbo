<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo extends DataEntity
{
    public const FOLDER_PHOTOS = 'albums/photos/originaux';
    public const FOLDER_THUMBS = 'albums/photos/thumbs';
    public const FOLDER_LIGHTS = 'albums/photos/lights';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $filename = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateAt = null;

    #[ORM\OneToMany(mappedBy: 'photo', targetEntity: GroupPhotos::class, orphanRemoval: true)]
    private Collection $groupPhotos;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
        $this->groupPhotos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    public function getFile(): string
    {
        return $this->getFileOrDefault($this->filename, self::FOLDER_PHOTOS);
    }

    public function getFileThumb(): string
    {
        return $this->getFileOrDefault("thumbs-" . $this->filename, self::FOLDER_THUMBS);
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

    public function getDateAt(): ?\DateTimeInterface
    {
        return $this->dateAt;
    }

    public function setDateAt(?\DateTimeInterface $dateAt): self
    {
        $this->dateAt = $dateAt;

        return $this;
    }

    /**
     * @return Collection<int, GroupPhotos>
     */
    public function getGroupPhotos(): Collection
    {
        return $this->groupPhotos;
    }

    public function addGroupPhoto(GroupPhotos $groupPhoto): self
    {
        if (!$this->groupPhotos->contains($groupPhoto)) {
            $this->groupPhotos[] = $groupPhoto;
            $groupPhoto->setPhoto($this);
        }

        return $this;
    }

    public function removeGroupPhoto(GroupPhotos $groupPhoto): self
    {
        if ($this->groupPhotos->removeElement($groupPhoto)) {
            // set the owning side to null (unless already changed)
            if ($groupPhoto->getPhoto() === $this) {
                $groupPhoto->setPhoto(null);
            }
        }

        return $this;
    }
}
