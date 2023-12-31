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
use Automattic\WooCommerce\Client;

class WooCommerceChannelOrderRepositoryFactory implements ChannelEntityRepositoryFactoryInterface
{

    public function __construct(
        private AccessTokenProviderInterface $provider,
        private CacheInterface $cache,
        private AdditionalServiceRepository $additionalServiceRepository,
        private ProductRepository $productRepository,
        private CodeGeneratorInterface $codeGenerator,
        private WooCommerceUrlFactory $urlFactory,
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

       
        $metadata = $channel->getMetadata();
        $baseUrl = $metadata['base_url'] ?? '';
        $clientId = $metadata['client_id'] ?? '';
        $clientSecret = $metadata['client_secret'] ?? '';

        $client = new Client(
            $baseUrl,
            $clientId,
            $clientSecret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'query_string_auth' => true,
                'verify_ssl' => false,
            ]
        );



        return new WooCommerceChannelHttpOrderRepository(
            channel: $channel,
            httpClient: $this->client,
            cache: $this->cache,
            productRepository: $this->productRepository,
            additionalServiceRepository: $this->additionalServiceRepository,
            codeGenerator: $this->codeGenerator,
        );
    }
}
