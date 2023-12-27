<?php

namespace App\Entity\Shipment;

use App\Repository\Shipment\ShipmentEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentEventRepository::class)]
class ShipmentEvent
{
    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column(length: 128)]
    private ?string $code = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column(length: 32)]
    private ?string $type = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column(length: 128)]
    private ?string $title = null;

    #[Groups(['shipment_event:list','shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[Groups(['shipment_event:with_metadata', 'shipment_event:write'])]
    #[ORM\Column(nullable: true)]
    private ?array $metadata = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $eventOccuredAt = null;

    #[Groups(['shipment_event:list', 'shipment_event:read', 'shipment_event:write'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->eventOccuredAt = $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getEventOccuredAt(): ?\DateTimeImmutable
    {
        return $this->eventOccuredAt;
    }

    public function setEventOccuredAt(\DateTimeImmutable $eventOccuredAt): static
    {
        $this->eventOccuredAt = $eventOccuredAt;

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
}
