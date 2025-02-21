<?php

namespace XenoLab\XenoEngine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use XenoLab\XenoEngine\Repository\XenoCategoryRepository;

#[ORM\Entity(repositoryClass: XenoCategoryRepository::class)]
class XenoCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $title = [];

    #[ORM\Column]
    private array $slug = [];

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, XenoArticle>
     */
    #[ORM\OneToMany(targetEntity: XenoArticle::class, mappedBy: 'category')]
    private Collection $articles;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'categories')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $categories;

    /**
     * @var Collection<int, XenoLibrary>
     */
    #[ORM\ManyToMany(targetEntity: XenoLibrary::class, mappedBy: 'category')]
    private Collection $libraries;

    #[ORM\Column]
    private array $seo = [];

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->libraries = new ArrayCollection();
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

    public function setSlug(array $slug): array
    {
        $this->slug = $slug;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, XenoArticle>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(XenoArticle $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategory($this);
        }

        return $this;
    }

    public function removeArticle(XenoArticle $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setParent($this);
        }

        return $this;
    }

    public function removeCategory(self $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }

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
            $library->addCategory($this);
        }

        return $this;
    }

    public function removeLibrary(XenoLibrary $library): static
    {
        if ($this->libraries->removeElement($library)) {
            $library->removeCategory($this);
        }

        return $this;
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
}
