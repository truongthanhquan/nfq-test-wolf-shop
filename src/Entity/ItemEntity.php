<?php

declare(strict_types=1);

namespace App\Entity;

use App\Item;
use App\Repository\ItemEntityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemEntityRepository::class)]
class ItemEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $sellIn = null;

    #[ORM\Column]
    private ?int $quality = null;

    #[ORM\Column(nullable: true)]
    private ?array $attributes = null;

    #[ORM\Column(nullable: true)]
    private ?array $image = null;

    public function __toItem(): Item
    {
        $item = new Item((string) $this->name, (int) $this->sellIn, (int) $this->quality);
        if (! empty($this->getImageUrl())) {
            $item->setImgUrl($this->getImageUrl());
        }

        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSellIn(): ?int
    {
        return $this->sellIn;
    }

    public function setSellIn(int $sellIn): static
    {
        $this->sellIn = $sellIn;

        return $this;
    }

    public function getQuality(): ?int
    {
        return $this->quality;
    }

    public function setQuality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image['url'] ?? null;
    }
}
