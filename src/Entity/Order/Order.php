<?php

namespace App\Entity\Order;

use App\Entity\Addressing\Address;
use App\Entity\Channel\Channel;
use App\Entity\Shipment\ShipmentFulfilment;
use App\Repository\Order\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $status = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(length: 3, nullable: true)]
    private ?string $currency = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(nullable: true)]
    private ?int $total = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(nullable: true)]
    private ?array $customerData = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(nullable: true)]
    private ?bool $paid = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column]
    private array $metadata = [];

    #[Groups(['order:with_items','order:write'])]
    #[ORM\OneToMany(mappedBy: '_order', targetEntity: OrderItem::class, orphanRemoval: true)]
    private Collection $items;

    #[Groups(['order:with_address','order:write'])]
    #[ORM\ManyToOne]
    private ?Address $shippingAddress = null;

    #[Groups(['order:with_address','order:write'])]
    #[ORM\ManyToOne]
    private ?Address $billingAddress = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $channelOrderId = null;

    #[Groups(['order:with_additional_services', 'order:read','order:write'])]
    #[ORM\ManyToMany(targetEntity: AdditionalService::class)]
    private Collection $additionalServices;

    #[Groups(['order:with_channel', 'order:read','order:write'])]
    #[ORM\ManyToOne]
    private ?Channel $channel = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $channelOrderNumber = null;

    #[Groups(['order:list', 'order:read','order:write'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $channelOrderCreatedAt = null;

    #[Groups(['order:with_fulfilment','order:write'])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?ShipmentFulfilment $fulfilment = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $paymentMethodCode = null;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->additionalServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getCustomerData(): ?array
    {
        return $this->customerData;
    }

    public function setCustomerData(?array $customerData): static
    {
        $this->customerData = $customerData;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(?bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): static
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress): static
    {
        $this->shippingAddress = $shippingAddress;

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

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getChannelOrderNumber(): ?string
    {
        return $this->channelOrderNumber;
    }

    public function setChannelOrderNumber(?string $channelOrderNumber): static
    {
        $this->channelOrderNumber = $channelOrderNumber;

        return $this;
    }

    public function getChannelOrderCreatedAt(): ?\DateTimeImmutable
    {
        return $this->channelOrderCreatedAt;
    }

    public function setChannelOrderCreatedAt(?\DateTimeImmutable $channelOrderCreatedAt): static
    {
        $this->channelOrderCreatedAt = $channelOrderCreatedAt;

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

    public function getPaymentMethodCode(): ?string
    {
        return $this->paymentMethodCode;
    }

    public function setPaymentMethodCode(?string $paymentMethodCode): static
    {
        $this->paymentMethodCode = $paymentMethodCode;

        return $this;
    }
}
