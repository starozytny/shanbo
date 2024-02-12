<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album extends DataEntity
{
    public const FOLDER_ALBUMS = 'albums';

    public const ALBUMS_READ = ['album_read'];

    public const ACCESS_PRIVATE = 0;
    public const ACCESS_PUBLIC = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['album_read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['album_read'])]
    private ?string $name = null;

    #[Slug(fields: ['name'], updatable: true)]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['album_read'])]
    private ?string $cover = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['album_read'])]
    private ?int $access = self::ACCESS_PUBLIC;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Group::class, orphanRemoval: true)]
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getAccess(): ?int
    {
        return $this->access;
    }

    public function setAccess(int $access): self
    {
        $this->access = $access;

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

    public function getCoverFile(): string
    {
        return $this->getFileOrDefault($this->cover, self::FOLDER_ALBUMS);
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setAlbum($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            // set the owning side to null (unless already changed)
            if ($group->getAlbum() === $this) {
                $group->setAlbum(null);
            }
        }

        return $this;
    }
}
