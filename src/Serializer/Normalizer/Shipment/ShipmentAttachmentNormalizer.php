<?php

namespace App\Serializer\Normalizer\Shipment;

use App\Entity\Shipment\ShipmentAttachment;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShipmentAttachmentNormalizer implements NormalizerInterface//, CacheableSupportsMethodInterface
{
    public function __construct(
        // #[Target('serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private HttpFoundationExtension $httpHelper,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @param ShipmentAttachment $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $reference = $object->getReference();
        if ($reference) {
            $data['url'] = $this->generateUrl($reference);
        }
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof ShipmentAttachment;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes($format): array{
        return [
            ShipmentAttachment::class => true
        ];
    }



    private function generateUrl($reference): string
    {
        return $this->getAbsoluteUrl($reference);
    }

    private function getRelativeUrl(string $path): string
    {
        return $this->urlGenerator->generate('app_file_read', ['path' => $path]);
    }

    private function getAbsoluteUrl(string $path): string
    {
        $relativeUrl = $this->getRelativeUrl($path);
        return $this->httpHelper->generateAbsoluteUrl($relativeUrl);
    }
}
