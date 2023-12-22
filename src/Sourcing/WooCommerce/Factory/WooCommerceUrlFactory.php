<?php

namespace App\Sourcing\WooCommerce\Factory;

use App\Entity\Channel\Channel;

class WooCommerceUrlFactory
{

    public function __construct()
    {
    }

    public function createUrl(Channel $channel, string $endpoint, string $version = 'v3'): string
    {
        $metadata = $channel->getMetadata();
        $baseUrl = $metadata['base_url'] ?? '';
        if (empty($baseUrl)) {
            throw new \LogicException('Base url is not set for channel ' . $channel->getName());
        }
        $baseUrl = rtrim($baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');

        $url = $baseUrl . '/wp-json/wc/' . $version . '/' . $endpoint;
        return $url;
    }
}
