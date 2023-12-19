<?php

namespace App\Entity\Addressing;

use App\Entity\Composition\Timestamps;
use App\Repository\Addressing\CoordinateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: CoordinateRepository::class)]
class Coordinate
{
    use Timestamps {
        __construct as private constructTimestamps;
    }

    #[Groups(['address:read', 'address:list', 'coordinate:read', 'coordinate:write'])]
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[Groups(['address:read', 'address:list', 'coordinate:read', 'coordinate:write'])]
    #[ORM\Column]
    private ?float $latitude = null;

    #[Groups(['address:read', 'address:list', 'coordinate:read', 'coordinate:write'])]
    #[ORM\Column]
    private ?float $longitude = null;

    #[Groups(['address:read', 'address:list', 'coordinate:read', 'coordinate:write'])]
    #[ORM\Column(nullable: true)]
    private ?float $altitude = null;

    #[Groups(['address:read', 'address:list', 'coordinate:read', 'coordinate:write'])]
    #[ORM\Column(nullable: true)]
    private ?float $accuracy = null;


    public function __construct(?float $latitude = null, ?float $longitude = null, ?float $altitude = null, ?float $accuracy = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->altitude = $altitude;
        $this->accuracy = $accuracy;

        $this->constructTimestamps();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function setAltitude(float $altitude): static
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getAccuracy(): ?float
    {
        return $this->accuracy;
    }

    public function setAccuracy(?float $accuracy): static
    {
        $this->accuracy = $accuracy;

        return $this;
    }
}
