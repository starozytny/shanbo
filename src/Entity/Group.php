<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Table(name: '`group`')]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
class Group
{
    public const GROUP_REAAD = ['grp:read'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['grp:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['grp:read'])]
    private $name;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private $album;

    #[ORM\OneToMany(targetEntity: GroupPhotos::class, mappedBy: 'grp', orphanRemoval: true)]
    private $groupPhotos;

    public function __construct()
    {
        $this->groupPhotos = new ArrayCollection();
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

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

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
            $groupPhoto->setGrp($this);
        }

        return $this;
    }

    public function removeGroupPhoto(GroupPhotos $groupPhoto): self
    {
        if ($this->groupPhotos->removeElement($groupPhoto)) {
            // set the owning side to null (unless already changed)
            if ($groupPhoto->getGrp() === $this) {
                $groupPhoto->setGrp(null);
            }
        }

        return $this;
    }
}
