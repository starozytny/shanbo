<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo extends DataEntity
{
    const FOLDER_PHOTOS = 'albums/photos/originaux';
    const FOLDER_THUMBS = 'albums/photos/thumbs';
    const FOLDER_LIGHTS = 'albums/photos/lights';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAt;

    /**
     * @ORM\OneToMany(targetEntity=GroupPhotos::class, mappedBy="photo", orphanRemoval=true)
     */
    private $groupPhotos;

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

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->getFileOrDefault($this->filename, self::FOLDER_PHOTOS);
    }

    /**
     * @return string
     */
    public function getFileThumb(): string
    {
        return $this->getFileOrDefault("thumbs-" . $this->filename, self::FOLDER_THUMBS);
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
