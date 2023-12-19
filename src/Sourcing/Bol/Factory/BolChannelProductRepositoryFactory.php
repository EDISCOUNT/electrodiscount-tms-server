<?php

namespace App\Sourcing\Bol\Factory;

use App\Entity\Channel\Channel;
use App\Sourcing\Bol\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Bol\Repository\BolChannelHttpOrderRepository;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;

class BolChannelProductRepositoryFactory implements ChannelEntityRepositoryFactoryInterface
{

    public function __construct(
        private AccessTokenProviderInterface $provider,
        private HttpClientInterface $client,
    ) {
    }
    /**
     * @return RepositoryInterface<Order>
     */
    public function create(
        Channel $channel,
        array $metadata = []
    ): RepositoryInterface {
        return new BolChannelHttpOrderRepository(
            channel: $channel,
            tokenProvider: $this->provider,
            httpClient: $this->client,
        );
    }
}
