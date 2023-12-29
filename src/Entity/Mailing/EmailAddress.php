<?php

namespace App\Entity\Mailing;

use App\Repository\Mailing\EmailAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'mailing_email_addresses')]
#[ORM\Entity(repositoryClass: EmailAddressRepository::class)]
class EmailAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailAddress = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $fullName = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }
}
