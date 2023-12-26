<?php

namespace App\Entity\Addressing;

use App\Entity\Account\User;
use App\Entity\Composition\Timestamps;
use App\Repository\Addressing\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    use Timestamps;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 12)]
    private ?string $postcode = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 3)]
    private ?string $countryCode = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $provinceName = null;


    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailAddress = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Coordinate $coordinate = null;

    #[Groups(['address:read', 'address:list',])]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $googlePlaceId = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $provinceCode = null;

    public function getId(): ?Ulid
    {
        return $this->id;
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getProvinceName(): ?string
    {
        return $this->provinceName;
    }

    public function setProvinceName(?string $provinceName): static
    {
        $this->provinceName = $provinceName;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

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

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): static
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getCoordinate(): ?Coordinate
    {
        return $this->coordinate;
    }

    public function setCoordinate(?Coordinate $coordinate): static
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlaceId;
    }

    public function setGooglePlaceId(?string $googlePlaceId): static
    {
        $this->googlePlaceId = $googlePlaceId;

        return $this;
    }

    public function getFormattedRepresentaion(): string
    {
        $formatted = '';

        if ($this->street)
            $formatted .= ($formatted ? ($formatted ? ', ' : '') : '') . $this->street;

        if ($this->postcode)
            $formatted .=  ($formatted ? ($formatted ? ', ' : '') : '') . $this->postcode;

        if ($this->city)
            $formatted .= ($formatted ? ', ' : '') . $this->city;

        if ($this->provinceName)
            $formatted .= ($formatted ? ', ' : '') . $this->provinceName;

        if ($code = $this->countryCode) {
            if (strlen($code) === 3)
                $formatted .= ($formatted ? ', ' : '') . Countries::getAlpha3Name($code);
            elseif (strlen($code) === 2)
                $formatted .= ($formatted ? ', ' : '') . Countries::getAlpha3Name(Countries::getAlpha3Code($code));
        }

        return $formatted;
        // return trim(sprintf('%s, %s, %s, %s', $this->street, $this->city, $this->provinceName, $this->countryCode));
    }

    public function getProvinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function setProvinceCode(?string $provinceCode): static
    {
        $this->provinceCode = $provinceCode;

        return $this;
    }
}
