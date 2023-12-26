<?php

namespace App\Entity\Account;

use App\Entity\Carrier\Carrier;
use App\Repository\Account\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[Groups(['user:with_roles', 'user:write'])]
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $firstName = null;

    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $lastName = null;

    #[Groups(['user:with_carrier', 'user:write'])]
    #[ORM\OneToOne(mappedBy: 'operatorUser', cascade: ['persist', 'remove'])]
    private ?Carrier $carrier = null;

    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $email = null;

    #[Groups(['user:list','user:read', 'user:write'])]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if($this->getCarrier()){
            $roles[] = 'ROLE_CARRIER_OPERATOR';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCarrier(): ?Carrier
    {
        return $this->carrier;
    }

    public function setCarrier(?Carrier $carrier): static
    {
        // unset the owning side of the relation if necessary
        if ($carrier === null && $this->carrier !== null) {
            $this->carrier->setOperatorUser(null);
        }

        // set the owning side of the relation if necessary
        if ($carrier !== null && $carrier->getOperatorUser() !== $this) {
            $carrier->setOperatorUser($this);
        }

        $this->carrier = $carrier;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
