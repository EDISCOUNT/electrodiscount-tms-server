<?php

namespace App\Sourcing\Bol\Repository;

use App\Entity\Addressing\Address;
use App\Entity\Catalog\Product;
use App\Entity\Channel\Channel;
use App\Entity\Order\AdditionalService;
use App\Entity\Shipment\ShipmentFulfilment;
use App\Sourcing\Repository\RepositoryInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Repository\Catalog\ProductRepository;
use App\Repository\Order\AdditionalServiceRepository;
use App\Service\Util\CodeGeneratorInterface;
use App\Sourcing\Bol\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Exception\EntityNotFoundException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @template T
 * @template ID
 */
class BolChannelHttpOrderRepository implements RepositoryInterface
{
    public function __construct(
        private AccessTokenProviderInterface $tokenProvider,
        private CacheInterface $cache,
        private HttpClientInterface $httpClient,
        private AdditionalServiceRepository $additionalServiceRepository,
        private ProductRepository $productRepository,
        private CodeGeneratorInterface $codeGenerator,
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

        $data = $this->doGetOrderPage($page, $limit, $criteria, $orderBy);

        // $collection = $data['orders']; //array_map(fn (array $data) => $this->buildOrder($data), $data['orders']);

        $collection = array_map(fn (array $data) => $this->buildOrder(
            data: $this->doGetOrderItem($data['orderId']),
            channel: $this->channel,
            orderSummary: $data,
        ), $data['orders'] ?? []);
        $pagination = new Pagerfanta(new ArrayAdapter($collection));
        $pagination
            ->setCurrentPage($page)
            ->setMaxPerPage($limit);
        return $pagination;
    }

    /**
     * @param string|int $id
     * @return Order
     * @throws EntityNotFoundException
     */
    public function getById(string|int $id): mixed
    {

        $data = $this->doGetOrderItem($id);
        // return $data;
        return $this->buildOrder($data, $this->channel);
    }




    private function buildOrder(array $data, Channel $channel, ?array $orderSummary = null): Order
    {
        $order = new Order();
        $this->mapOrder($order, $data, $orderSummary);
        $order->setChannel($channel);
        return $order;
    }
    private function mapOrder(Order $order, array $data, ?array $orderSummary = null): void
    {
        $order
            ->setChannelOrderId($data['orderId'])
            ->setChannelOrderCreatedAt(new \DateTimeImmutable($data['orderPlacedDateTime'] ?? ''));
        // 
        foreach (($data['orderItems'] ?? []) as $itemData) {
            $orderItemSummary = $this->getOrderItemSummary($orderSummary, $itemData['orderItemId']);

            $item = $this->buildOrderItem($itemData, $orderItemSummary);
            $order->addItem($item);
        }

        if (isset($data['shipmentDetails'])) {
            $address = $this->buildAddress($data['shipmentDetails']);
            $order->setShippingAddress($address);
        }

        if (isset($data['billingDetails'])) {
            $address = $this->buildAddress($data['billingDetails']);
            $order->setBillingAddress($address);
        }

        if (isset($data['additionalServices'])) {
            foreach ($data['additionalServices'] as $addData) {
                $service = $this->getAdditionalService($addData);
            }
            $order->addAdditionalService($service);
        }
        // additionalServices
    }


    private function buildOrderItem(array $data, ?array $orderItemSummary = null): OrderItem
    {
        $orderItem = new OrderItem();
        $this->mapOrderItem($orderItem, $data, $orderItemSummary);
        return $orderItem;
    }
    private function mapOrderItem(OrderItem $orderItem, array $data, ?array $orderItemSummary = null): void
    {
        $orderItem
            ->setChannelOrderItemId($data['orderItemId'])
            ->setUnitPrice($data['unitPrice'] ?? '')
            ->setQuantity($data['quantity'])
            ->setStatus($data['fulfilmentStatus'] ?? null)
            ->setQuantityShipped($data['quantityShipped'])
            ->setQuantityCancelled($data['quantityShipped']);


        if ($orderItemSummary) {
            $orderItem
                ->setStatus($orderItemSummary['fulfilmentStatus'] ?? null);
        }


        if (isset($data['fulfilment'])) {
            $fData = $data['fulfilment'];

            $method = $fData['method'] ?? null;

            if ($method != 'FBR') {
                throw new \Exception("Fulfilment method is not FBR");
            }

            $fulfilment = new ShipmentFulfilment();
            $fulfilment
                ->setMethod($fData['method'] ?? null)
                ->setDistributionParty($fData['distributionParty'] ?? null)
                ->setLatestDeliveryDate(isset($fData['latestDeliveryDate']) ? new \DateTime($fData['latestDeliveryDate']) : null)
                ->setExactDeliveryDate(isset($fData['exactDeliveryDate']) ? new \DateTime($fData['exactDeliveryDate']) : null)
                ->setExpiryDate(isset($fData['expiryDate']) ? new \DateTime($fData['expiryDate']) : null)
                ->setTimeFrameType($fData['timeFrameType'] ?? null);

            $orderItem
                ->setFulfilment($fulfilment);
        }



        if (isset($data['product'])) {
            $pData = $data['product'];

            $product = null;


            $gtin = $pData['ean'] ?? null;
            if ($gtin) {
                $product = $this->productRepository->findOneBy([
                    'gtin' => $gtin,
                ]);
            }
            //
            if (null == $product) {

                $code = $this->codeGenerator->generateCode(length: 8, prefix: 'BOL_');
                $product = new Product();
                $product
                    ->setCode($code)
                    ->setGtin($pData['ean'] ?? null)
                    ->setName($pData['title']);
            }

            $orderItem
                ->setProduct($product);
        }
    }




    private function buildAddress(array $data): Address
    {
        $address = new Address();
        $this->mapAddress($address, $data);
        return $address;
    }
    private function mapAddress(Address $address, array $data): void
    {
        $street = $this->buildStreet($data);
        $address
            ->setFirstName($data['firstName'] ?? null)
            ->setLastName($data['surname'] ?? null)
            ->setFirstName($data['firstName'] ?? null)
            ->setEmailAddress($data['email'])
            ->setPhoneNumber($data['deliveryPhoneNumber'] ?? $data['phone'] ?? null)
            ->setStreet($street)
            ->setPostcode($data['zipCode'] ?? '')
            ->setCountryCode($data['countryCode'] ?? '')
            ->setCity($data['city'] ?? '')
            ->setCompany($data['company'] ?? '');
    }

    private function buildStreet(array $data)
    {
        $street = sprintf("%s %s %s", $data['houseNumberExtension'] ?? '',  $data['houseNumber'] ?? '', $data['streetName'] ?? '',);
        return trim($street);
    }



    private function getAdditionalService(array $data): AdditionalService
    {
        $code = $data['serviceType'];
        $service = $this->additionalServiceRepository->findOneBy(['code' => $code]);

        if (null == $service) {
            $service = new AdditionalService();

            $title = str_replace('_', ' ', $code); // replace underscores with spaces
            $title = ucwords(strtolower($title)); // convert to capitalized case
            $title = str_replace(' ', '_', $title); // 

            $service
                ->setCode($code)
                ->setTitle($title);
        }
        return $service;
    }




    // public function buildCascadeOrderPage(int $page = 1, $limit = 10, $criteria = [], $orderBy = []): array
    // {
    //     $maxItemsPerpage = 50;
    //     // $totalItems
    // }



    public function doGetOrderPage(int $page = 1, int $limit = 10, $criteria = [], $orderBy = []): array
    {
        if($limit < 0){
            $limit = 1000;
        }
        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);
        // $httpClient = HttpClient::create();
        $maxItemsPerPage = 50;
        $numRequests = ceil($limit / $maxItemsPerPage);
        $requests = [];
        $orders = [];

        $status = 'OPEN';

        if (isset($criteria['status'])) {
            $status = $this->buildStausQuery($criteria['status']);
        }


        for ($i = 0; $i < $numRequests; $i++) {
            $requests[] = $this->httpClient->request('GET', 'https://api.bol.com/retailer/orders', [
                'query' => [
                    'page' => $page + $i,
                    'fulfilment-method' => 'ALL',
                    'status' => $status,
                    // 'fulfilment-method' => 'FBR',
                    // 'limit' => $maxItemsPerPage,
                    // add other query parameters as needed
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $authToken,
                    'Accept' => 'application/vnd.retailer.v10+json',
                    // 'Accept' => 'application/json',
                ],
            ]);
        }

        foreach ($this->httpClient->stream($requests) as $response => $chunk) {
            if ($chunk->isLast()) {
                $data = $response->toArray();
                if (isset($data['orders'])) {
                    $orders = array_merge($orders, $data['orders']);
                }
                // $results = array_merge($results, $data);
            }
        }
        return [
            'orders' => $orders,
        ];
    }

    // public function doGetOrderPage(int $page = 1, $limit = 10, $criteria = [], $orderBy = []): array
    // {

    //     $metadata = $this->channel->getMetadata();
    //     $clientId = $metadata['client_id'];
    //     if (isset($criteria['status'])) {
    //         $criteria['status'] = $this->buildStausQuery($criteria['status']);
    //     }
    //     $criteria['page'] = $page;
    //     $params = http_build_query($criteria);

    //     $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);
    //     $url = "https://api.bol.com/retailer/orders?fulfilment-method=FBR&{$params}";
    //     $key = 'url-' . md5($url . serialize($criteria). '-' . $clientId);

    //     return $this->cache->get($key, function (ItemInterface $item) use ($url, $authToken) {
    //         $item->expiresAfter(20);    // 20 seconds
    //         $result = $this->httpClient
    //             ->request(
    //                 "GET",
    //                 $url,
    //                 [
    //                     // 'timeout' => 150,
    //                     'headers' => [
    //                         'Authorization' => 'Bearer ' . $authToken,
    //                         'Accept' => 'application/vnd.retailer.v10+json',
    //                         // 'Accept' => 'application/json',
    //                     ],
    //                 ]
    //             );

    //         $data = $result->toArray(throw: true);
    //         return $data;
    //     });
    // }

    public function doGetOrderItem(mixed $id): array
    {
        $metadata = $this->channel->getMetadata();
        $clientId = $metadata['client_id'];

        $authToken = $this->tokenProvider->getAccessTokenForChannel($this->channel);
        $url = "https://api.bol.com/retailer/orders/{$id}";
        $key = 'url-' . md5($url . '-' . $clientId);

        return $this->cache->get($key, function (ItemInterface $item) use ($url, $authToken) {
            $item->expiresAfter(60 * 5);    // 5 minutes
            $result = $this->httpClient
                ->request(
                    "GET",
                    $url,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $authToken,
                            'Accept' => 'application/vnd.retailer.v10+json',
                        ],
                    ]
                );
            $data = $result->toArray(throw: true);
            return $data;
        });
    }



    private function getOrderItemSummary(?array $orderSummary, string | int $orderItemId): ?array
    {
        if ($orderSummary) {
            foreach ($orderSummary['orderItems'] as $item) {
                if ($item['orderItemId'] == $orderItemId) {
                    return $item;
                }
            }
        }
        return null;
    }

    private function buildStausQuery(string $status): array|string|null
    {
        $map = [
            'open' => 'OPEN',
            'shipped' => 'SHIPPED',
            'all' => 'ALL',
        ];
        if (isset($map[$status]))
            return $map[$status];
        return null;
    }
}
