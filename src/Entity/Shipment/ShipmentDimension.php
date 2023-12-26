<?php

namespace App\Entity\Shipment;

use App\Repository\Shipment\ShipmentDimensionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentDimensionRepository::class)]
class ShipmentDimension
{
    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column]
    private ?int $length = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column]
    private ?int $width = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column]
    private ?int $height = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $unit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
