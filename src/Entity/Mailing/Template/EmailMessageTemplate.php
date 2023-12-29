<?php

namespace App\Entity\Mailing\Template;

use App\Entity\Account\User;
use App\Repository\Mailing\Template\EmailMessageTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EmailMessageTemplateRepository::class)]
class EmailMessageTemplate
{
    #[Groups(['message_template:list', 'message_template:read', 'message_template:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['message_template:list', 'message_template:read', 'message_template:write'])]
    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[Groups(['message_template:list', 'message_template:read', 'message_template:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[Groups(['message_template:list', 'message_template:read', 'message_template:write'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[Groups(['message_template:list', 'message_template:read', 'message_template:write'])]
    #[ORM\Column]
    private ?bool $enabled = false;

    #[Groups(['message_template:with_owner', 'message_template:write'])]
    #[ORM\ManyToOne]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
