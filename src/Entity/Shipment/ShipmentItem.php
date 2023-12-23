<?php

namespace App\Entity\Shipment;

use App\Entity\Catalog\Product;
use App\Entity\Inventory\Storage;
use App\Entity\Order\OrderItem;
use App\Repository\Shipment\ShipmentItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentItemRepository::class)]
class ShipmentItem
{
    #[Groups(['shipment_item:read_with_order', 'shipment_item:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment_item:with_product', 'shipment_item:write'])]
    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Product $product = null;

    #[Groups(['shipment_item:read', 'shipment_item:write'])]
    #[ORM\Column]
    private ?int $quantity = null;

    #[Groups(['shipment_item:read', 'shipment_item:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $quantityReturned = null;

    #[Groups(['shipment_item:with_order_item', 'shipment_item:write'])]
    #[ORM\ManyToOne]
    private ?OrderItem $orderItem = null;

    #[Groups(['shipment_item:read', 'shipment_item:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $internalOrderItemId = null;

    #[Groups(['shipment_item:with_storage', 'shipment_item:write'])]
    #[ORM\ManyToOne]
    private ?Storage $storage = null;

    #[Groups(['shipment_item:with_shipment', 'shipment_item:write'])]
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shipment $shipment = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ShipmentFulfilment $fulfilment = null;

    #[Groups(['shipment_item:list', 'shipment_item:read', 'shipment_item:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $name = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityReturned(): ?int
    {
        return $this->quantityReturned;
    }

    public function setQuantityReturned(?int $quantityReturned): static
    {
        $this->quantityReturned = $quantityReturned;

        return $this;
    }

    public function getOrderItem(): ?OrderItem
    {
        return $this->orderItem;
    }

    public function setOrderItem(?OrderItem $orderItem): static
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    public function getInternalOrderItemId(): ?string
    {
        return $this->internalOrderItemId;
    }

    public function setInternalOrderItemId(?string $internalOrderItemId): static
    {
        $this->internalOrderItemId = $internalOrderItemId;

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

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): static
    {
        $this->shipment = $shipment;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
    
}
