<?php

namespace App\Serializer\Normalizer\Carrier;


// use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\Carrier\Carrier;
use App\Sourcing\ShipmentSourceManager;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class CarrierNormalizer implements NormalizerInterface //, NormalizerAwareInterface //, CacheableSupportsMethodInterface
{
    // use NormalizerAwareTrait;
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager,
        private HttpFoundationExtension $httpHelper,
        private UrlGeneratorInterface $urlGenerator,
        private NormalizerInterface $normalizer,
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {

        $data = $this->normalizer->normalize($object, $format, $context);

        $logo = $data['logoImage'] ?? null;
        if ($logo) {
            $url = $this->getRelativeUrl($logo);
            $data['logoImage'] = [
                'url' => $url,
                'reference' => $logo,
            ];
        }


        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Carrier;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes($format): array
    {
        return [
            Carrier::class => true,
        ];
    }



    private function getRelativeUrl(string $path): string
    {
        return $this->urlGenerator->generate(
            'app_file_read',
            ['path' => $path],
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
