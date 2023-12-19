<?php

namespace App\Entity\Order;

use App\Entity\Catalog\Product;
use App\Repository\Order\OrderItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: AdditionalService::class)]
    private Collection $additionalService;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelProductId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channelVariantId = null;

    #[ORM\ManyToOne]
    private ?Product $product = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityShipped = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityCancelled = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $unitPrice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sku = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $total = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $_order = null;

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
}
