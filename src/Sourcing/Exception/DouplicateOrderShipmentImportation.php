<?php
namespace App\Sourcing\Exception;

use App\Entity\Channel\Channel;
use App\Entity\Shipment\Shipment;

class DouplicateOrderShipmentImportation extends \LogicException{
    public function __construct(
        private Channel $channel,
        private Shipment $shipment,
        private string $channelOrderId,
    ){
        parent::__construct(
            sprintf(
                'Order with channel code "%s" and channel order id "%s" already has a shipment with code "%s" and id "%s"',
                $channel->getName(),
                $channelOrderId,
                $shipment->getCode(),
                $shipment->getId(),
            )
        );
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getChannelOrderId(): string
    {
        return $this->channelOrderId;
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }
}