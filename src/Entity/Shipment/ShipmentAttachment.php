<?php

namespace App\Entity\Shipment;

use App\Repository\Shipment\ShipmentAttachmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentAttachmentRepository::class)]
class ShipmentAttachment
{
    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $reference = null;

    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $size = null;

    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $caption = null;

    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Column(length: 64)]
    private ?string $type = null;

    #[Groups(['shipment_attachment:list', 'shipment_attachment:read', 'shipment_event:write'])]
    #[ORM\Column(nullable: true)]
    private ?array $meta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

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

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;

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

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
