<?php

namespace App\Entity\Shipment;

use App\Repository\Shipment\ShipmentFulfilmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShipmentFulfilmentRepository::class)]
class ShipmentFulfilment
{

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(length: 8, nullable: true)]
    private ?string $method = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $distributionParty = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $latestDeliveryDate = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $exactDeliveryDate = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $expiryDate = null;

    #[Groups(['shipment_fulfilment:list', 'shipment_fulfilment:read', 'shipment_fulfilment:write'])]
    #[ORM\Column(length: 16, nullable: true)]
    private ?string $timeFrameType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getDistributionParty(): ?string
    {
        return $this->distributionParty;
    }

    public function setDistributionParty(?string $distributionParty): static
    {
        $this->distributionParty = $distributionParty;

        return $this;
    }

    public function getLatestDeliveryDate(): ?\DateTimeInterface
    {
        return $this->latestDeliveryDate;
    }

    public function setLatestDeliveryDate(?\DateTimeInterface $latestDeliveryDate): static
    {
        $this->latestDeliveryDate = $latestDeliveryDate;

        return $this;
    }

    public function getExactDeliveryDate(): ?\DateTimeInterface
    {
        return $this->exactDeliveryDate;
    }

    public function setExactDeliveryDate(?\DateTimeInterface $exactDeliveryDate): static
    {
        $this->exactDeliveryDate = $exactDeliveryDate;

        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(\DateTimeInterface $expiryDate): static
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getTimeFrameType(): ?string
    {
        return $this->timeFrameType;
    }

    public function setTimeFrameType(?string $timeFrameType): static
    {
        $this->timeFrameType = $timeFrameType;

        return $this;
    }



    public function copy(): ShipmentFulfilment
    {
        $fulfilment =  new ShipmentFulfilment();

        $fulfilment
            ->setMethod($this->getMethod())
            ->setTimeFrameType($this->getTimeFrameType())
            ->setExactDeliveryDate($this->getExactDeliveryDate())
            ->setDistributionParty($this->getDistributionParty())
            ->setExpiryDate($this->getExpiryDate())
            ->setLatestDeliveryDate($this->getLatestDeliveryDate());

        return $fulfilment;
    }
}
