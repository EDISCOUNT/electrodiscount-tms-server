<?php

namespace App\Sourcing\WooCommerce\Factory;

use App\Entity\Channel\Channel;
use App\Sourcing\WooCommerce\Authentication\AccessTokenProviderInterface;
use App\Sourcing\WooCommerce\Repository\WooCommerceChannelHttpOrderRepository;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;
use App\Repository\Catalog\ProductRepository;
use App\Repository\Order\AdditionalServiceRepository;
use App\Service\Util\CodeGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;

class WooCommerceChannelOrderRepositoryFactory implements ChannelEntityRepositoryFactoryInterface
{

    public function __construct(
        private AccessTokenProviderInterface $provider,
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private AdditionalServiceRepository $additionalServiceRepository,
        private ProductRepository $productRepository,
        private CodeGeneratorInterface $codeGenerator,
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

        return new WooCommerceChannelHttpOrderRepository(
            channel: $channel,
            tokenProvider: $this->provider,
            httpClient: $this->client,
            cache: $this->cache,
            additionalServiceRepository: $this->additionalServiceRepository,
            productRepository: $this->productRepository,
            codeGenerator: $this->codeGenerator,
            baseURL: $baseURL,
        );
    }
}
