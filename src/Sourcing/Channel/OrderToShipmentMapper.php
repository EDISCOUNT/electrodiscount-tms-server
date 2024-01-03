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
        // $prefix = $order->getChannelOrderId();
        // if ($prefix) {
        //     $prefix .= '_';
        // }

        $code = $this->codeGenerator->generateCode(length: 8);
        $channel = $order->getChannel();
        $shipment = new Shipment();


        $shipment
            ->setSourceId($channel->getType())
            ->setCode($code)
            ->setChannel($channel)
            ->setChannelOrderId($order->getChannelOrderId())
            ->setDestinationAddress($order->getShippingAddress())
            ->setBillingAddress($order->getBillingAddress())
            ->setFulfilment($order->getFulfilment()?->copy());

        foreach ($order->getItems() as $orderItem) {
            $shipmentItem = $this->buildShipmentItem($orderItem);
            $shipment->addItem($shipmentItem);
        }


        $shipment->getAdditionalServices()->clear();
        foreach ($order->getAdditionalServices() as $service) {
            $shipment->addAdditionalService($service);
        }



        return $shipment;
    }


    private function buildShipmentItem(OrderItem $orderItem): ShipmentItem
    {
        $shipmentItem = new ShipmentItem();

        $shipmentItem
            ->setName($orderItem->getName())
            ->setProduct($orderItem->getProduct())
            ->setQuantity($orderItem->getQuantity())
            ->setInternalOrderItemId($orderItem->getId())
            ->setFulfilment($orderItem->getFulfilment()?->copy());

        return $shipmentItem;
    }
}
