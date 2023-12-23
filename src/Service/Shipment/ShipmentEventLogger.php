<?php

namespace App\Service\Shipment;

use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentEvent;
use App\Service\Util\CodeGeneratorInterface;

class ShipmentEventLogger
{


    public const EVENT_SHIPMENT_CREATED = 'shipment.created';
    public const EVENT_SHIPMENT_CREATED_MANUALLY = 'shipment.created.manually';
    public const EVENT_SHIPMENT_IMPORTED = 'shipment.imported';
    public const EVENT_SHIPMENT_UPDATED = 'shipment.updated';


    public function __construct(
        private CodeGeneratorInterface $codeGenerator
    ) {
    }



    public function logCreated(Shipment $shipment): void
    {

        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment Created")
            ->setType(self::EVENT_SHIPMENT_CREATED);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }

    public function logImported(Shipment $shipment, ?Channel $channel = null, ?Order $order = null): void
    {

        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment Imported")
            ->setType(self::EVENT_SHIPMENT_IMPORTED);

        if ($channel) {
            $subtitle = sprintf(
                "Shipment was import from %s for order %s",
                $channel?->getName() ?? '[N/A]',
                $order?->getChannelOrderId() ?? '[N/A]'
            );
            $event->setSubtitle($subtitle);
        }
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }


    public function logUpdated(Shipment $shipment, array $data = []): void
    {
        $event = new ShipmentEvent();
        $event
            ->setTitle("Shipment was updated")
            ->setType(self::EVENT_SHIPMENT_UPDATED)
            ->setMetadata([
                'data' => $data,
            ]);
        $shipment->addEvent($event);

        $this->finalize(
            event: $event,
            shipment: $shipment,
        );
    }



    private function finalize(ShipmentEvent $event, Shipment $shipment): void
    {
        $code = $this->codeGenerator->generateCode(length: 12);
        $event
            ->setCode($code);
    }
}
