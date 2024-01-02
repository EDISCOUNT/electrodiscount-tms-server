<?php

namespace App\Sourcing\WooCommerce\Channel;

use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Sourcing\WooCommerce\Authentication\AccessTokenProviderInterface;
use App\Sourcing\Channel\ChannelSourceManager;
use App\Sourcing\Channel\OrderToShipmentMapper;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\WooCommerce\Authentication\WooCommereAuthenticator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WooCommerceChannelSourceManager extends ChannelSourceManager
{


    // private WooCommereAuthenticator $authenticator;

    public function __construct(
        private ChannelEntityRepositoryFactoryInterface $orderRepositoryFactory,
        private ChannelEntityRepositoryFactoryInterface $productRepositoryFactory,
        private OrderToShipmentMapper $orderToShipmentMapper,
        private HttpClientInterface $httpClient,
        private array $config = [],

    ) {
        parent::__construct(
            $orderRepositoryFactory,
            $productRepositoryFactory,
            $orderToShipmentMapper,
            $config
        );
    }

    public function commitShipment(Shipment $shipment, Order $order): mixed
    {
        $reference = $shipment->getCode();
        $shipmentInfo = $this->createShipmentForOrder($order, $reference);
        return $shipmentInfo;
    }

    private function createShipmentForOrder(Order $order, ?string $reference = null): array
    {

        $channel = $order->getChannel();
        $authenticator = new WooCommereAuthenticator($channel);
        $baseURL = $authenticator->getURL();
        $id = $order->getChannelOrderId();

        $url = sprintf('%s%s/%s', $baseURL, 'orders', $id);

        try {
            $response = $this->httpClient->request('PATCH', $url, [
                ...$authenticator->authenticate($url, 'PATCH', []),
                'json' => [
                    'status' => 'completed',
                ]
            ]);

            $result = $response->toArray();
            return $result;
            // Your code here
        } catch (\Throwable $err) {
            throw $err;
        }
    }
}
