<?php

namespace Xenolabs\XenoEngine\Entity;

use Xenolabs\XenoEngine\Abstract\XenoEngineUser;
use Xenolabs\XenoEngine\Entity\Element;
use Xenolabs\XenoEngine\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['route'])]
#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?XenoEngineUser $createdBy = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?XenoEngineUser $updatedBy = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Ged>
     */
    #[ORM\ManyToMany(targetEntity: Ged::class, mappedBy: 'page')]
    private Collection $geds;

    #[ORM\Column]
    private ?bool $published = null;

    /**
     * @var Collection<int, Element>
     */
    #[ORM\OneToMany(targetEntity: Element::class, mappedBy: 'page', orphanRemoval: true)]
    private Collection $elements;

    public function __construct()
    {
        $this->geds = new ArrayCollection();
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

    public function getCreatedBy(): ?XenoEngineUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?XenoEngineUser $createdBy): static
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

    public function getUpdatedBy(): ?XenoEngineUser
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?XenoEngineUser $updatedBy): static
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
     * @return Collection<int, Ged>
     */
    public function getGeds(): Collection
    {
        return $this->geds;
    }

    public function addGed(Ged $ged): static
    {
        if (!$this->geds->contains($ged)) {
            $this->geds->add($ged);
            $ged->addPage($this);
        }

        return $this;
    }

    public function removeGed(Ged $ged): static
    {
        if ($this->geds->removeElement($ged)) {
            $ged->removePage($this);
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
     * @return Collection<int, Element>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setPage($this);
        }

        return $this;
    }

    public function removeElement(Element $element): static
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
