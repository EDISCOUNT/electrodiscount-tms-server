<?php

namespace App\Sourcing\Channel;

use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use App\Service\Util\CodeGeneratorInterface;

class OrderToShipmentMapper
{


    public function __construct(
        private CodeGeneratorInterface $codeGenerator
    ) {
    }


    public function map(Order $order): Shipment
    {
        $prefix = $order->getChannelOrderId();
        if ($prefix) {
            $prefix .= '_';
        }

        $code = $this->codeGenerator->generateCode(length: 3, prefix: $prefix);
        $channel = $order->getChannel();
        $shipment = new Shipment();


        $shipment
            ->setSourceId($channel->getType())
            ->setCode($code)
            ->setChannel($channel)
            ->setChannelOrderId($order->getChannelOrderId())
            ->setDestinationAddress($order->getShippingAddress());

        foreach ($order->getItems() as $orderItem) {
            $shipmentItem = $this->buildShipmentItem($orderItem);
            $shipment->addItem($shipmentItem);
        }



        return $shipment;
    }


    private function buildShipmentItem(OrderItem $orderItem): ShipmentItem
    {
        $shipmentItem = new ShipmentItem();

        $shipmentItem
            ->setProduct($orderItem->getProduct())
            ->setQuantity($orderItem->getQuantity())
            ->setInternalOrderItemId($orderItem->getId());

        return $shipmentItem;
    }
}
