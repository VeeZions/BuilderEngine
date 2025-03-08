<?php

namespace VeeZions\BuilderEngine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VeeZions\BuilderEngine\Enum\LibraryEnum;
use VeeZions\BuilderEngine\Repository\BuilderLibraryRepository;

#[UniqueEntity(fields: ['url'])]
#[ORM\Entity(repositoryClass: BuilderLibraryRepository::class)]
class BuilderLibrary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, BuilderPage>
     */
    #[ORM\ManyToMany(targetEntity: BuilderPage::class, inversedBy: 'libraries')]
    private Collection $page;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[Assert\NotBlank]
    #[ORM\Column(enumType: LibraryEnum::class)]
    private ?LibraryEnum $type = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $mime = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legend = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, BuilderArticle>
     */
    #[ORM\ManyToMany(targetEntity: BuilderArticle::class, inversedBy: 'libraries')]
    private Collection $article;

    /**
     * @var Collection<int, BuilderCategory>
     */
    #[ORM\ManyToMany(targetEntity: BuilderCategory::class, inversedBy: 'libraries')]
    private Collection $category;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $width = null;

    #[ORM\Column(nullable: true)]
    private ?int $height = null;

    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    /**
     * @var Collection<int, BuilderElement>
     */
    #[ORM\OneToMany(targetEntity: BuilderElement::class, mappedBy: 'bgImage')]
    private Collection $elements;

    public function __construct()
    {
        $this->page = new ArrayCollection();
        $this->article = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, BuilderPage>
     */
    public function getPage(): Collection
    {
        return $this->page;
    }

    public function addPage(BuilderPage $page): static
    {
        if (!$this->page->contains($page)) {
            $this->page->add($page);
        }

        return $this;
    }

    public function removePage(BuilderPage $page): static
    {
        $this->page->removeElement($page);

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?LibraryEnum
    {
        return $this->type;
    }

    public function setType(LibraryEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): static
    {
        $this->mime = $mime;

        return $this;
    }

    public function getLegend(): ?string
    {
        return $this->legend;
    }

    public function setLegend(?string $legend): static
    {
        $this->legend = $legend;

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

    /**
     * @return Collection<int, BuilderArticle>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(BuilderArticle $article): static
    {
        if (!$this->article->contains($article)) {
            $this->article->add($article);
        }

        return $this;
    }

    public function removeArticle(BuilderArticle $article): static
    {
        $this->article->removeElement($article);

        return $this;
    }

    /**
     * @return Collection<int, BuilderCategory>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(BuilderCategory $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(BuilderCategory $category): static
    {
        $this->category->removeElement($category);

        return $this;
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

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection<int, BuilderElement>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(BuilderElement $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setBgImage($this);
        }

        return $this;
    }

    public function removeElement(BuilderElement $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getBgImage() === $this) {
                $element->setBgImage(null);
            }
        }

        return $this;
    }
}
