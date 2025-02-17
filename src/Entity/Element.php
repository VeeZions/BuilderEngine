<?php

namespace Xenolabs\XenoEngine\Entity;

use Xenolabs\XenoEngine\Entity\Ged;
use Xenolabs\XenoEngine\Entity\Page;
use Xenolabs\XenoEngine\Repository\ElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementRepository::class)]
class Element
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Page $page = null;

    #[ORM\Column(length: 100)]
    private ?string $builderId = null;

    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?Ged $bgImage = null;

    /**
     * @var Collection<int, Ged>
     */
    #[ORM\ManyToMany(targetEntity: Ged::class)]
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

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
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

    public function getBgImage(): ?Ged
    {
        return $this->bgImage;
    }

    public function setBgImage(?Ged $bgImage): static
    {
        $this->bgImage = $bgImage;

        return $this;
    }

    /**
     * @return Collection<int, Ged>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Ged $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
        }

        return $this;
    }

    public function removeMedium(Ged $medium): static
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
