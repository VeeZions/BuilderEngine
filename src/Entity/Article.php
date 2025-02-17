<?php

namespace Xenolabs\XenoEngineBundle\Entity;

use App\Entity\User;
use Xenolabs\XenoEngineBundle\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $title = [];

    #[ORM\Column]
    private array $slug = [];

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Category $category = null;
    
    #[ORM\Column]
    private array $seo = [];

    #[ORM\Column]
    private array $content = [];

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $author = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, Ged>
     */
    #[ORM\ManyToMany(targetEntity: Ged::class, mappedBy: 'article')]
    private Collection $geds;

    public function __construct()
    {
        $this->geds = new ArrayCollection();
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

    public function getSlug(): array
    {
        return $this->slug;
    }

    public function setSlug(array $slug): static
    {
        $this->slug = $slug;
        
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getSeo(): array
    {
        return $this->seo;
    }

    /**
     * @param array<string, string> $seo
     */
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

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

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
            $ged->addArticle($this);
        }

        return $this;
    }

    public function removeGed(Ged $ged): static
    {
        if ($this->geds->removeElement($ged)) {
            $ged->removeArticle($this);
        }

        return $this;
    }
}
