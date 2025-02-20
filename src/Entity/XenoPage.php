<?php

namespace XenoLab\XenoEngine\Entity;

use XenoLab\XenoEngine\Repository\XenoPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['route'])]
#[ORM\Entity(repositoryClass: XenoPageRepository::class)]
class XenoPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $title = [];
    
    #[ORM\Column]
    private array $seo = [];
    
    #[ORM\Column]
    private array $content = [];

    #[ORM\Column(length: 255)]
    private ?string $route = null;

    #[ORM\Column(nullable: true)]
    private ?int $createdBy = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $updatedBy = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, XenoLibrary>
     */
    #[ORM\ManyToMany(targetEntity: XenoLibrary::class, mappedBy: 'page')]
    private Collection $libraries;

    #[ORM\Column]
    private ?bool $published = null;

    /**
     * @var Collection<int, XenoElement>
     */
    #[ORM\OneToMany(targetEntity: XenoElement::class, mappedBy: 'page', orphanRemoval: true)]
    private Collection $elements;

    public function __construct()
    {
        $this->libraries = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function setTitle(array $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSeo(): array
    {
        return $this->seo;
    }
    
    public function setSeo(array $seo): static
    {
        $this->seo = $seo;

        return $this;
    }
    
    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?int $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?int $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, XenoLibrary>
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(XenoLibrary $library): static
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->addPage($this);
        }

        return $this;
    }

    public function removeLibrary(XenoLibrary $library): static
    {
        if ($this->libraries->removeElement($library)) {
            $library->removePage($this);
        }

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Collection<int, XenoElement>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(XenoElement $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setPage($this);
        }

        return $this;
    }

    public function removeElement(XenoElement $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getPage() === $this) {
                $element->setPage(null);
            }
        }

        return $this;
    }
}
