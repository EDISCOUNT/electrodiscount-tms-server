<?php
namespace App\Entity\Shipment;

enum ShipmentFulfilmentType: string
{
        //  case PICKUP_AT_STORE = 'PICKUP_AT_STORE';
        //     case DELIVER_TO_ADDRESS = 'DELIVER_TO_ADDRESS';

    case PICKUP_AND_DELIVER = 'PICKUP_AND_DELIVERY';
    case DROPSHIPPING = 'DROPSHIPPING';

    case RETURN_ORDER = 'RETURN_ORDER';

    case EXCHANGE_ORDER = 'EXCHANGE_ORDER';
}
