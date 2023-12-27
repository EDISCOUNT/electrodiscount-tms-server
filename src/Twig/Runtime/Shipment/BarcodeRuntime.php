<?php

namespace App\Twig\Runtime\Shipment;

use App\Entity\Shipment\Shipment;
use Twig\Extension\RuntimeExtensionInterface;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function doSomething($value)
    {
        // ...
    }

    public function generateShipmentBarcocde(Shipment $shipment,): string{
        
        $generator = new BarcodeGeneratorSVG();
        $shipmentCode = $shipment->getCode();

        // $color = [255, 0, 0];
        $barcode = $generator->getBarcode($shipmentCode, $generator::TYPE_CODE_128, 3, 80,);
        return $barcode;
    }

    public function base64_encode(string $data): string{
        return base64_encode($data);
    }
}
