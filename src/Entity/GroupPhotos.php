<?php

namespace App\Entity;

use App\Repository\GroupPhotosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupPhotosRepository::class)]
class GroupPhotos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'groupPhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private $grp;

    #[ORM\ManyToOne(targetEntity: Photo::class, inversedBy: 'groupPhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private $photo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrp(): ?Group
    {
        return $this->grp;
    }

    public function setGrp(?Group $grp): self
    {
        $this->grp = $grp;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
