<?php
namespace App\Sourcing\Bol\Repository;

use App\Entity\Channel\Channel;
use App\Sourcing\Repository\RepositoryInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;
use App\Sourcing\Bol\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Exception\EntityNotFoundException;

/**
 * @template T
 * @template ID
 */
class BolChannelHttpOrderRepository implements RepositoryInterface
{
    public function __construct(
        private AccessTokenProviderInterface $tokenProvider,
        private HttpClientInterface $httpClient,
        private Channel $channel,
    ) {
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $criteria
     * @param array $orderBy
     * @return Pagerfanta<T>
     */
    public function paginate($page = 1, $limit = 10, $criteria = [], $orderBy = []): Pagerfanta
    {

        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);

        $result = $this->httpClient->request("GET", "https://api.bol.com/retailer/orders", [
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
     * @return Order
     * @throws EntityNotFoundException
     */
    public function getById(string|int $id): mixed
    {
        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);

        $result = $this->httpClient->request("GET", "https://api.bol.com/retailer/orders/{$id}", [
            'timeout' => 150,
            'headers' => [
                'Authorization' => 'Bearer ' . $authToken,
                'Accept' => 'application/vnd.retailer.v10+json',
                // 'Accept' => 'application/json',
            ],
        ]);

        $data = $result->toArray(throw: true);

        return new Order();
    }
}
