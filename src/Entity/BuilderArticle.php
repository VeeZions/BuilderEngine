<?php

namespace VeeZions\BuilderEngine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use VeeZions\BuilderEngine\Repository\BuilderArticleRepository;

#[ORM\Entity(repositoryClass: BuilderArticleRepository::class)]
class BuilderArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Slug(fields: ['title'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 12)]
    private ?string $locale = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column]
    private array $seo = [];

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private bool $published = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $author = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $createdBy = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $updatedBy = null;

    /**
     * @var Collection<int, BuilderLibrary>
     */
    #[ORM\ManyToMany(targetEntity: BuilderLibrary::class, mappedBy: 'article')]
    private Collection $libraries;

    /**
     * @var Collection<int, BuilderCategory>
     */
    #[ORM\ManyToMany(targetEntity: BuilderCategory::class, inversedBy: 'articles')]
    private Collection $categories;

    public function __construct()
    {
        $this->libraries = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSeo(): array
    {
        return $this->seo;
    }

    /**
     * @param array<string, mixed> $seo
     */
    public function setSeo(array $seo): static
    {
        $this->seo = $seo;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?int
    {
        return $this->author;
    }

    public function setAuthor(?int $author): static
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

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?int $createdBy): static
    {
        $this->createdBy = $createdBy;

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

    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?int $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(BuilderLibrary $library): static
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries->add($library);
            $library->addArticle($this);
        }

        return $this;
    }

    public function removeLibrary(BuilderLibrary $library): static
    {
        if ($this->libraries->removeElement($library)) {
            $library->removeArticle($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, BuilderCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(BuilderCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(BuilderCategory $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
