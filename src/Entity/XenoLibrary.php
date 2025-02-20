<?php

namespace XenoLab\XenoEngine\Entity;

use XenoLab\XenoEngine\Enum\LibraryEnum;
use XenoLab\XenoEngine\Repository\XenoLibraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['url'])]
#[ORM\Entity(repositoryClass: XenoLibraryRepository::class)]
class XenoLibrary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, XenoPage>
     */
    #[ORM\ManyToMany(targetEntity: XenoPage::class, inversedBy: 'libraries')]
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
     * @var Collection<int, XenoArticle>
     */
    #[ORM\ManyToMany(targetEntity: XenoArticle::class, inversedBy: 'libraries')]
    private Collection $article;

    /**
     * @var Collection<int, XenoCategory>
     */
    #[ORM\ManyToMany(targetEntity: XenoCategory::class, inversedBy: 'libraries')]
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
     * @var Collection<int, XenoElement>
     */
    #[ORM\OneToMany(targetEntity: XenoElement::class, mappedBy: 'bgImage')]
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
     * @return Collection<int, XenoPage>
     */
    public function getPage(): Collection
    {
        return $this->page;
    }

    public function addPage(XenoPage $page): static
    {
        if (!$this->page->contains($page)) {
            $this->page->add($page);
        }

        return $this;
    }

    public function removePage(XenoPage $page): static
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
     * @return Collection<int, XenoArticle>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(XenoArticle $article): static
    {
        if (!$this->article->contains($article)) {
            $this->article->add($article);
        }

        return $this;
    }

    public function removeArticle(XenoArticle $article): static
    {
        $this->article->removeElement($article);

        return $this;
    }

    /**
     * @return Collection<int, XenoCategory>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(XenoCategory $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(XenoCategory $category): static
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
            $element->setBgImage($this);
        }

        return $this;
    }

    public function removeElement(XenoElement $element): static
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
