<?php

namespace App\Entity\Carrier;

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
}
