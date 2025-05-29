<?php

namespace VeeZions\BuilderEngine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VeeZions\BuilderEngine\Repository\BuilderNavigationRepository;

#[ORM\Entity(repositoryClass: BuilderNavigationRepository::class)]
class BuilderNavigation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 150)]
    private ?string $type = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column]
    private array $content = [];

    #[Assert\NotBlank]
    #[ORM\Column(length: 12)]
    private ?string $locale = null;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return array<string, mixed>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array<string, mixed> $content
     */
    public function setContent(array $content): static
    {
        $this->content = $content;

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
}
