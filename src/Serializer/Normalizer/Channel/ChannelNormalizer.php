<?php

namespace App\Serializer\Normalizer\Channel;


// use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\Channel\Channel;
use App\Sourcing\ShipmentSourceManager;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ChannelNormalizer implements NormalizerInterface, NormalizerAwareInterface //, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager,
        // private ObjectNormalizer $normalizer,
    ) {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {

        $data = $this->doNormalize($object, $format, $context);

        $typeId = $data['type'] ?? null;
        if ($typeId) {
            $config = $this->shipmentSourceManager->getSources()[$typeId] ?? null;
            if ($config) {
                $data['typeConfig'] = $config;
                $data['type'] = $typeId;
            }
        }


        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Channel;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    public function getSupportedTypes($format): array
    {
        return [
            Channel::class => true,
        ];
    }



    /**
     * @param Channel channel
     */
    public function doNormalize($channel, string $format = null, array $context = []): array
    {

        return [
            'id' => $channel->getId(),
            'code' => $channel->getCode(),
            'name' => $channel->getName(),
            'type' => $channel->getType(),
            'enabled' => $channel->isEnabled(),
            'metadata' => $channel->getMetadata(),
            'shortDescription' => $channel->getShortDescription(),
            'description' => $channel->getDescription(),

        ];
    }
}
