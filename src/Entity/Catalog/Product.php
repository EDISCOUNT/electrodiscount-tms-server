<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[Groups(['product:list', 'product:read', 'product:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['product:list', 'product:read', 'product:write'])]
    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[Groups(['product:list', 'product:read', 'product:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $gtin = null;

    #[Groups(['product:list', 'product:read', 'product:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Groups(['product:with_price', 'product:read', 'product:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ProductPrice $price = null;

    #[Groups(['product:list', 'product:read', 'product:write'])]
    #[ORM\Column(nullable: true)]
    private ?bool $enabled = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getGtin(): ?string
    {
        return $this->gtin;
    }

    public function setGtin(?string $gtin): static
    {
        $this->gtin = $gtin;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?ProductPrice
    {
        return $this->price;
    }

    public function setPrice(?ProductPrice $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
}
