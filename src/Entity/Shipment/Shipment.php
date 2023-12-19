<?php

namespace App\Entity\Shipment;

use App\Entity\Addressing\Address;
use App\Repository\Shipment\ShipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[ORM\ManyToOne]
    private ?Address $originAddress = null;

    #[ORM\ManyToOne]
    private ?Address $destinationAddress = null;

    #[ORM\Column(length: 64)]
    private ?string $sourceId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idOnSorce = null;

    #[ORM\Column(length: 32)]
    private ?string $status = null;

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

    public function getOriginAddress(): ?Address
    {
        return $this->originAddress;
    }

    public function setOriginAddress(?Address $originAddress): static
    {
        $this->originAddress = $originAddress;

        return $this;
    }

    public function getDestinationAddress(): ?Address
    {
        return $this->destinationAddress;
    }

    public function setDestinationAddress(?Address $destinationAddress): static
    {
        $this->destinationAddress = $destinationAddress;

        return $this;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): static
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    public function getIdOnSorce(): ?string
    {
        return $this->idOnSorce;
    }

    public function setIdOnSorce(?string $idOnSorce): static
    {
        $this->idOnSorce = $idOnSorce;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
