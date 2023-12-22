<?php
namespace App\Sourcing\WooCommerce\Repository;

use App\Entity\Channel\Channel;
use App\Sourcing\Repository\RepositoryInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Catalog\Product;
use App\Sourcing\WooCommerce\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Exception\EntityNotFoundException;
use App\Sourcing\WooCommerce\Factory\WooCommerceUrlFactory;

/**
 * @template T
 * @template ID
 */
class WooCommerceChannelHttpProductRepository implements RepositoryInterface
{
    public function __construct(
        private AccessTokenProviderInterface $tokenProvider,
        private HttpClientInterface $httpClient,
        private string $baseURL,
        private Channel $channel,
    ) {
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $criteria
     * @param array $ProductBy
     * @return Pagerfanta<T>
     */
    public function paginate($page = 1, $limit = 10, $criteria = [], $ProductBy = []): Pagerfanta
    {

        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);

        $result = $this->httpClient->request("GET", "https://api.bol.com/retailer/Products", [
            'timeout' => 150,
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken,
                'Accept' => 'application/vnd.retailer.v10+json',
                // 'Accept' => 'application/json',
            ],
        ]);

        $data = $result->toArray(throw: true);

        $page = new Pagerfanta(new ArrayAdapter($data));
        return $page;
    }

    /**
     * @param string|int $id
     * @return Product
     * @throws EntityNotFoundException
     */
    public function getById(string|int $id): mixed
    {
        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);

        $result = $this->httpClient->request("GET", "https://api.bol.com/retailer/Products/{$id}", [
            'timeout' => 150,
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken,
                'Accept' => 'application/vnd.retailer.v10+json',
                // 'Accept' => 'application/json',
            ],
        ]);

        $data = $result->toArray(throw: true);

        return new Product();
    }
}
