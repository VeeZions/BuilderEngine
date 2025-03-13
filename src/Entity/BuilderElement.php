<?php

namespace VeeZions\BuilderEngine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use VeeZions\BuilderEngine\Repository\BuilderElementRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuilderElementRepository::class)]
class BuilderElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BuilderPage $page = null;

    #[ORM\Column(length: 100)]
    private ?string $builderId = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?BuilderLibrary $bgImage = null;

    /**
     * @var Collection<int, BuilderLibrary>
     */
    #[ORM\ManyToMany(targetEntity: BuilderLibrary::class)]
    private Collection $media;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $moduleType = null;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?BuilderPage
    {
        return $this->page;
    }

    public function setPage(?BuilderPage $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getBuilderId(): ?string
    {
        return $this->builderId;
    }

    public function setBuilderId(string $builderId): static
    {
        $this->builderId = $builderId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getBgImage(): ?BuilderLibrary
    {
        return $this->bgImage;
    }

    public function setBgImage(?BuilderLibrary $bgImage): static
    {
        $this->bgImage = $bgImage;

        return $this;
    }

    /**
     * @return Collection<int, BuilderLibrary>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(BuilderLibrary $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
        }

        return $this;
    }

    public function removeMedium(BuilderLibrary $medium): static
    {
        $this->media->removeElement($medium);

        return $this;
    }

    public function getModuleType(): ?string
    {
        return $this->moduleType;
    }

    public function setModuleType(?string $moduleType): static
    {
        $this->moduleType = $moduleType;

        return $this;
    }
}
