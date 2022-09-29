<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
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
        return $this->getFileOrDefault($this->filename, self::FOLDER_THUMBS);
    }
    /**
     * @return string
     */
    public function getFileLight(): string
    {
        return $this->getFileOrDefault($this->filename, self::FOLDER_LIGHTS);
    }
}
