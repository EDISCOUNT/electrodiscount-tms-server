<?php

namespace App\Entity\Order;

use App\Repository\Order\AdditionalServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AdditionalServiceRepository::class)]
class AdditionalService
{
    #[Groups(['additional_service:list', 'additional_service:read', 'additional_service:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['additional_service:list', 'additional_service:read', 'additional_service:write'])]
    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[Groups(['additional_service:list', 'additional_service:read', 'additional_service:write'])]
    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[Groups(['additional_service:list', 'additional_service:read', 'additional_service:write'])]
    #[ORM\Column]
    private ?bool $enabled = null;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
