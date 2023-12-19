<?php

namespace App\Serializer\Normalizer\Shipment;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\Shipment\Shipment;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;

class ShipmentNormalizer implements NormalizerInterface, NormalizerAwareInterface//, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        // private ObjectNormalizer $normalizer,
        // private Serializer $serializer
        )
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Shipment;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes($format): array{
        return [
            Shipment::class
        ];
    }
}
