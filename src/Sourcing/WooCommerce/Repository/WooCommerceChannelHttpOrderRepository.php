<?php

namespace App\Sourcing\WooCommerce\Repository;

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
use App\Sourcing\WooCommerce\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Exception\EntityNotFoundException;
use App\Sourcing\WooCommerce\Factory\WooCommerceUrlFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Automattic\WooCommerce\Client;

/**
 * @template T
 * @template ID
 */
class WooCommerceChannelHttpOrderRepository implements RepositoryInterface
{
    public function __construct(
        private CacheInterface $cache,
        private AdditionalServiceRepository $additionalServiceRepository,
        private ProductRepository $productRepository,
        private CodeGeneratorInterface $codeGenerator,
        private Channel $channel,
        private Client $client,
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
            data: $this->doGetOrderItem($data['id']),
            channel: $this->channel,
        ), $data);
        $page = new Pagerfanta(new ArrayAdapter($collection));
        return $page;
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




    private function buildOrder(array $data, Channel $channel): Order
    {
        $order = new Order();
        $this->mapOrder($order, $data);
        $order->setChannel($channel);
        return $order;
    }
    private function mapOrder(Order $order, array $data): void
    {
        $order
            ->setChannelOrderId($data['id'])
            ->setStatus($data['status' ?? 'pending'])
            ->setChannelOrderCreatedAt(new \DateTimeImmutable($data['date_created'] ?? ''))
            // ->setChannelOrderNumber($data['number'])
        ;
        // 
        foreach (($data['line_items'] ?? []) as $itemData) {
            $item = $this->buildOrderItem($itemData);
            $order->addItem($item);
        }

        if (isset($data['shipping'])) {
            $address = $this->buildAddress($data['shipping']);
            $order->setShippingAddress($address);
        }

        if (isset($data['billing'])) {
            $address = $this->buildAddress($data['billing']);
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


    private function buildOrderItem(array $data): OrderItem
    {
        $orderItem = new OrderItem();
        $this->mapOrderItem($orderItem, $data);
        return $orderItem;
    }
    private function mapOrderItem(OrderItem $orderItem, array $data): void
    {
        $orderItem
            ->setChannelOrderItemId($data['id'])
            ->setUnitPrice($data['price'] ?? '')
            ->setQuantity($data['quantity'] ?? 1)
            // ->setProductTitle($data['name'] ?? '')
            ->setQuantityShipped($data['quantityShipped'] ?? 0)
            ->setQuantityCancelled($data['quantityCancelled'] ?? 0);


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



        $orderItem
            ->setChannelProductId($data['product_id'] ?? null)
            ->setChannelVariantId($data['variation_id'] ?? null)
            ->setName($data['name'] ?? null);

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
            ->setFirstName($data['first_name'] ?? null)
            ->setLastName($data['last_name'] ?? null)
            ->setEmailAddress($data['email'] ?? null)
            ->setPhoneNumber($data['deliveryPhoneNumber'] ?? $data['phone'] ?? null)
            ->setStreet($street)
            ->setProvinceName($data['state'] ?? '')
            ->setPostcode($data['postcode'] ?? '')
            ->setCountryCode($data['country'] ?? '')
            ->setCity($data['city'] ?? '')
            ->setCompany($data['company'] ?? '');
    }

    private function buildStreet(array $data)
    {
        $street = sprintf("%s %s %s", $data['address_2'] ?? '',  $data['address_1'] ?? '', $data['streetName'] ?? '',);
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



    public function doGetOrderPage(int $page = 1, $limit = 10, $criteria = [], $orderBy = []): array
    {
        $metadata = $this->channel->getMetadata();
        $clientId = $metadata['client_id'];

        $key = 'woo-orders-' . md5(serialize($criteria) . $clientId . serialize($orderBy) . $page . $limit);

        return $this->cache->get($key, function (ItemInterface $item) use ($limit, $page) {
            $item->expiresAfter(60 * 5);    // 5 minutes
            $result = $this->client->get('orders', [
                'per_page' => $limit,
                'page' => $page,
            ]);
            return  json_decode(json_encode($result), true);
        });
    }

    public function doGetOrderItem(mixed $id): array
    {

        $metadata = $this->channel->getMetadata();
        $clientId = $metadata['client_id'];

        $key = 'woo-order-' . $clientId  . $id;

        return $this->cache->get($key, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(60 * 5);    // 5 minutes
            $result = $this->client->get('orders/' . $id);
            return  json_decode(json_encode($result), true);
        });
    }
}
