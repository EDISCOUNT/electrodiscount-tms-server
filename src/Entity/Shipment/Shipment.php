<?php

namespace App\Entity\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Channel\Channel;
use App\Entity\Inventory\Storage;
use App\Repository\Shipment\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{

    public const STATUS_NEW = 'new';
    public const STATUS_READY = 'ready';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';


    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[Groups(['shipment:with_address', 'shipment:read', 'shipment:write'])]
    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Address $originAddress = null;

    #[Groups(['shipment:with_address', 'shipment:read', 'shipment:write'])]
    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Address $destinationAddress = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 64)]
    private ?string $sourceId = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idOnSorce = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 32)]
    private ?string $status = self::STATUS_NEW;

    #[Groups(['shipment:with_storage', 'shipment:read', 'shipment:write'])]
    #[ORM\ManyToOne]
    private ?Storage $storage = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $channelOrderId = null;

    #[Groups(['shipment:with_items', 'shipment:write'])]
    #[ORM\OneToMany(mappedBy: 'shipment', targetEntity: ShipmentItem::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $items;

    #[Groups(['shipment:with_channel', 'shipment:read', 'shipment:write'])]
    #[ORM\ManyToOne]
    private ?Channel $channel = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function setStorage(?Storage $storage): static
    {
        $this->storage = $storage;

        return $this;
    }

    public function getChannelOrderId(): ?string
    {
        return $this->channelOrderId;
    }

    public function setChannelOrderId(?string $channelOrderId): static
    {
        $this->channelOrderId = $channelOrderId;

        return $this;
    }

    /**
     * @return Collection<int, ShipmentItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ShipmentItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setShipment($this);
        }

        return $this;
    }

    public function removeItem(ShipmentItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getShipment() === $this) {
                $item->setShipment(null);
            }
        }

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): static
    {
        $this->channel = $channel;

        return $this;
    }
}
