<?php

namespace App\Entity\Order;

use App\Entity\Catalog\Product;
use App\Entity\Shipment\ShipmentFulfilment;
use App\Repository\Order\OrderItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['order_item:read_with_additional_service', 'order_item:write'])]
    #[ORM\ManyToMany(targetEntity: AdditionalService::class)]
    private Collection $additionalService;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelProductId = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelVariantId = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $quantityShipped = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $quantityCancelled = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $unitPrice = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sku = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $total = null;

    #[Groups(['order_item:read_with_order', 'order_item:write'])]
    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $_order = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $channelOrderItemId = null;

    #[Groups(['order_item:with_fulfilment', 'order_item:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ShipmentFulfilment $fulfilment = null;

    #[Groups(['order_item:list', 'order_item:read', 'order_item:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    public function __construct()
    {
        $this->additionalService = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, AdditionalService>
     */
    public function getAdditionalService(): Collection
    {
        return $this->additionalService;
    }

    public function addAdditionalService(AdditionalService $additionalService): static
    {
        if (!$this->additionalService->contains($additionalService)) {
            $this->additionalService->add($additionalService);
        }

        return $this;
    }

    public function removeAdditionalService(AdditionalService $additionalService): static
    {
        $this->additionalService->removeElement($additionalService);

        return $this;
    }

    public function getChannelProductId(): ?string
    {
        return $this->channelProductId;
    }

    public function setChannelProductId(?string $channelProductId): static
    {
        $this->channelProductId = $channelProductId;

        return $this;
    }

    public function getChannelVariantId(): ?string
    {
        return $this->channelVariantId;
    }

    public function setChannelVariantId(?string $channelVariantId): static
    {
        $this->channelVariantId = $channelVariantId;

        return $this;
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

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantityShipped(): ?int
    {
        return $this->quantityShipped;
    }

    public function setQuantityShipped(?int $quantityShipped): static
    {
        $this->quantityShipped = $quantityShipped;

        return $this;
    }

    public function getQuantityCancelled(): ?int
    {
        return $this->quantityCancelled;
    }

    public function setQuantityCancelled(?int $quantityCancelled): static
    {
        $this->quantityCancelled = $quantityCancelled;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->_order;
    }

    public function setOrder(?Order $_order): static
    {
        $this->_order = $_order;

        return $this;
    }

    public function getChannelOrderItemId(): ?string
    {
        return $this->channelOrderItemId;
    }

    public function setChannelOrderItemId(?string $channelOrderItemId): static
    {
        $this->channelOrderItemId = $channelOrderItemId;

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
