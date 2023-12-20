<?php

namespace App\Sourcing\Bol\Factory;

use App\Entity\Channel\Channel;
use App\Sourcing\Bol\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Bol\Repository\BolChannelHttpOrderRepository;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;
use App\Repository\Catalog\ProductRepository;
use App\Repository\Order\AdditionalServiceRepository;
use App\Service\Util\CodeGeneratorInterface;
use Doctrine\Common\Cache\Cache;
use Symfony\Contracts\Cache\CacheInterface;

class BolChannelOrderRepositoryFactory implements ChannelEntityRepositoryFactoryInterface
{

    public function __construct(
        private AccessTokenProviderInterface $provider,
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private AdditionalServiceRepository $additionalServiceRepository,
        private ProductRepository $productRepository,
        private CodeGeneratorInterface $codeGenerator
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
            cache: $this->cache,
            additionalServiceRepository: $this->additionalServiceRepository,
            productRepository: $this->productRepository,
            codeGenerator: $this->codeGenerator,

        );
    }
}
