<?php

namespace App\Sourcing\WooCommerce\Factory;

use App\Entity\Channel\Channel;
use App\Sourcing\WooCommerce\Authentication\AccessTokenProviderInterface;
use App\Sourcing\WooCommerce\Repository\WooCommerceChannelHttpOrderRepository;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;
use App\Sourcing\WooCommerce\Repository\WooCommerceChannelHttpProductRepository;
use App\Sourcing\WooCommerce\Factory\WooCommerceUrlFactory;

class WooCommerceChannelProductRepositoryFactory implements ChannelEntityRepositoryFactoryInterface
{

    public function __construct(
        private HttpClientInterface $client,
        private WooCommerceUrlFactory $urlFactory,
    ) {
    }
    /**
     * @return RepositoryInterface<Order>
     */
    public function create(
        Channel $channel,
        array $metadata = []
    ): RepositoryInterface {

        $baseURL = $this->urlFactory->createUrl(
            channel: $channel,
            endpoint: 'orders',
        );
        return new WooCommerceChannelHttpProductRepository(
            channel: $channel,
            httpClient: $this->client,
            baseURL: $baseURL,
        );
    }
}
