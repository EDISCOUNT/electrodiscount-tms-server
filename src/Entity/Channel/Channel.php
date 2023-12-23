<?php

namespace App\Entity\Channel;

use App\Repository\Channel\ChannelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
class Channel
{
    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column(length: 32, unique: true)]
    private ?string $code = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $shortDescription = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column(length: 255, nullable: true)]

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    private ?string $description = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column(length: 64)]
    private ?string $type = null;

    #[Groups(['channel:list', 'channel:read', 'channel:write'])]
    #[ORM\Column]
    private ?bool $enabled = null;

    #[Groups(['channel:with_metadata', 'channel:read', 'channel:write'])]
    #[ORM\Column]
    private array $metadata = [];

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

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }
}
