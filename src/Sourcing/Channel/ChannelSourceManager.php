<?php

namespace App\Sourcing\Channel;

use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Sourcing\Factory\ChannelEntityRepositoryFactoryInterface;
use App\Sourcing\Repository\RepositoryInterface;

abstract class ChannelSourceManager
{

    public function __construct(
        private ChannelEntityRepositoryFactoryInterface $orderRepositoryFactory,
        private ChannelEntityRepositoryFactoryInterface $productRepositoryFactory,
        private OrderToShipmentMapper $orderToShipmentMapper,
        private array $config = [],

    ) {
    }


    public function getOrderRepository(Channel $channel): RepositoryInterface
    {
        return $this->orderRepositoryFactory->create($channel);
    }

    public function getProductRepository(Channel $channel): RepositoryInterface
    {
        return $this->productRepositoryFactory->create($channel);
    }


    public function mapOrderToShipment(Order $order): Shipment{
        $shipment = $this->orderToShipmentMapper->map($order);
        return $shipment;
    }

    public abstract function commitShipment(Shipment $shipment, Order $order): mixed;



    public function getConfig(): array
    {
        return $this->config;
    }
    
}
