<?php

namespace App\Entity\Carrier;

use App\Entity\Account\User;
use App\Repository\Carrier\CarrierRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CarrierRepository::class)]
class Carrier
{
    #[Groups(['carrier:list', 'carrier:read', 'carrier:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['carrier:list', 'carrier:read', 'carrier:write'])]
    #[ORM\Column(length: 64)]
    private ?string $code = null;

    #[Groups(['carrier:list', 'carrier:read', 'carrier:write'])]
    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[Groups(['carrier:list', 'carrier:read', 'carrier:write'])]
    #[ORM\Column]
    private ?bool $enabled = false;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $emailAddress = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\OneToOne(inversedBy: 'carrier', cascade: ['persist', 'remove'])]
    private ?User $operatorUser = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): static
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getOperatorUser(): ?User
    {
        return $this->operatorUser;
    }

    public function setOperatorUser(?User $operatorUser): static
    {
        $this->operatorUser = $operatorUser;

        return $this;
    }
}
