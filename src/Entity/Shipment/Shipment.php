<?php

namespace App\Entity\Shipment;

use App\Entity\Addressing\Address;
use App\Entity\Carrier\Carrier;
use App\Entity\Channel\Channel;
use App\Entity\Inventory\Storage;
use App\Entity\Order\AdditionalService;
use App\Repository\Shipment\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentRepository::class)]
class Shipment
{
    public const STATUS_NEW = 'new';
    public const STATUS_ASSIGNED = 'assigned'; //pending on the carrier side
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_INTRANSIT = 'intransit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';


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
    #[ORM\Column(length: 64, nullable: true)]
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

    #[Groups(['shipment:with_carrier',  'shipment:write'])]
    #[ORM\ManyToOne]
    private ?Carrier $carrier = null;

    #[Groups(['shipment:with_fulfilment',  'shipment:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ShipmentFulfilment $fulfilment = null;

    #[Groups(['shipment:list', 'shipment:read',  'shipment:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $channelShipmentId = null;

    #[Groups(['shipment:with_additional_services', 'shipment:read', 'shipment:write'])]
    #[ORM\ManyToMany(targetEntity: AdditionalService::class, cascade: ['persist'])]
    private Collection $additionalServices;

    #[Groups(['shipment:with_events', 'shipment:write'])]
    #[ORM\ManyToMany(targetEntity: ShipmentEvent::class, cascade: ['persist', 'remove'])]
    private Collection $events;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $netWeight = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $volumetricWeight = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $codAmount = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 3, nullable: true)]
    private ?string $codCurrency = null;

    #[Groups(['shipment:with_dimension', 'shipment:read', 'shipment:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ShipmentDimension $dimension = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $bookedAt = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['shipment:with_address', 'shipment:read', 'shipment:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $billingAddress = null;

    #[Groups(['shipment:list', 'shipment:read', 'shipment:write'])]
    #[ORM\Column(length: 32, options: ['default' => 'PICKUP_AND_DELIVERY'])]
    private ShipmentFulfilmentType $fulfilmentType = ShipmentFulfilmentType::PICKUP_AND_DELIVER;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->additionalServices = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    public function setCarrier(?Carrier $carrier): static
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getFulfilment(): ?ShipmentFulfilment
    {
        return $this->fulfilment;
    }

    public function setFulfilment(?ShipmentFulfilment $fulfilment): static
    {
        $this->fulfilment = $fulfilment;

        return $this;
    }

    public function getChannelShipmentId(): ?string
    {
        return $this->channelShipmentId;
    }

    public function setChannelShipmentId(?string $channelShipmentId): static
    {
        $this->channelShipmentId = $channelShipmentId;

        return $this;
    }

    /**
     * @return Collection<int, AdditionalService>
     */
    public function getAdditionalServices(): Collection
    {
        return $this->additionalServices;
    }

    public function addAdditionalService(AdditionalService $additionalService): static
    {
        if (!$this->additionalServices->contains($additionalService)) {
            $this->additionalServices->add($additionalService);
        }

        return $this;
    }

    public function removeAdditionalService(AdditionalService $additionalService): static
    {
        $this->additionalServices->removeElement($additionalService);

        return $this;
    }

    /**
     * @return Collection<int, ShipmentEvent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(ShipmentEvent $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    public function removeEvent(ShipmentEvent $event): static
    {
        $this->events->removeElement($event);

        return $this;
    }

    public function getNetWeight(): ?int
    {
        return $this->netWeight;
    }

    public function setNetWeight(?int $netWeight): static
    {
        $this->netWeight = $netWeight;

        return $this;
    }

    public function getVolumetricWeight(): ?int
    {
        return $this->volumetricWeight;
    }

    public function setVolumetricWeight(?int $volumetricWeight): static
    {
        $this->volumetricWeight = $volumetricWeight;

        return $this;
    }

    public function getCodAmount(): ?int
    {
        return $this->codAmount;
    }

    public function setCodAmount(?int $codAmount): static
    {
        $this->codAmount = $codAmount;

        return $this;
    }

    public function getCodCurrency(): ?string
    {
        return $this->codCurrency;
    }

    public function setCodCurrency(?string $codCurrency): static
    {
        $this->codCurrency = $codCurrency;

        return $this;
    }

    public function getDimension(): ?ShipmentDimension
    {
        return $this->dimension;
    }

    public function setDimension(?ShipmentDimension $dimension): static
    {
        $this->dimension = $dimension;

        return $this;
    }

    public function getBookedAt(): ?\DateTimeImmutable
    {
        return $this->bookedAt;
    }

    public function setBookedAt(?\DateTimeImmutable $bookedAt): static
    {
        $this->bookedAt = $bookedAt;

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

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Address $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getFulfilmentType(): ?ShipmentFulfilmentType
    {
        return $this->fulfilmentType;
    }

    public function setFulfilmentType(ShipmentFulfilmentType $fulfilmentType): static
    {
        $this->fulfilmentType = $fulfilmentType;

        return $this;
    }



    public function isDropship(){
        return $this->fulfilmentType === ShipmentFulfilmentType::DROPSHIPPING;
    }
}
