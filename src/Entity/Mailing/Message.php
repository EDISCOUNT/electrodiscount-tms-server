<?php

namespace App\Entity\Mailing;

use App\Repository\Mailing\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'mailing_messages')]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\JoinTable(name: 'mailing_message_recipients')]
    #[ORM\ManyToMany(targetEntity: EmailAddress::class, cascade: ['persist','remove'])]
    private Collection $recipients;

    #[ORM\JoinTable(name: 'mailing_message_cc_recipients')]
    #[ORM\ManyToMany(targetEntity: EmailAddress::class, cascade: ['persist','remove'])]
    private Collection $ccRecipients;

    #[ORM\JoinTable(name: 'mailing_message_bcc_recipients')]
    #[ORM\ManyToMany(targetEntity: EmailAddress::class, cascade: ['persist','remove'])]
    private Collection $bccRecipients;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->ccRecipients = new ArrayCollection();
        $this->bccRecipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, EmailAddress>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(EmailAddress $recipient): static
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }

        return $this;
    }

    public function removeRecipient(EmailAddress $recipient): static
    {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    /**
     * @return Collection<int, EmailAddress>
     */
    public function getCcRecipients(): Collection
    {
        return $this->ccRecipients;
    }

    public function addCcRecipient(EmailAddress $ccRecipient): static
    {
        if (!$this->ccRecipients->contains($ccRecipient)) {
            $this->ccRecipients->add($ccRecipient);
        }

        return $this;
    }

    public function removeCcRecipient(EmailAddress $ccRecipient): static
    {
        $this->ccRecipients->removeElement($ccRecipient);

        return $this;
    }

    /**
     * @return Collection<int, EmailAddress>
     */
    public function getBccRecipients(): Collection
    {
        return $this->bccRecipients;
    }

    public function addBccRecipient(EmailAddress $bccRecipient): static
    {
        if (!$this->bccRecipients->contains($bccRecipient)) {
            $this->bccRecipients->add($bccRecipient);
        }

        return $this;
    }

    public function removeBccRecipient(EmailAddress $bccRecipient): static
    {
        $this->bccRecipients->removeElement($bccRecipient);

        return $this;
    }
}
