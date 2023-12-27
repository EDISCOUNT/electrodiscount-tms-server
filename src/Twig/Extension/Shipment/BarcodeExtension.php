<?php

namespace App\Twig\Extension\Shipment;

use App\Twig\Runtime\Shipment\BarcodeRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BarcodeExtension extends AbstractExtension
{
    public function __construct(){
        // $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG(); // Vector based SVG
        // $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG(); // Pixel based PNG
        // $generatorJPG = new Picqer\Barcode\BarcodeGeneratorJPG(); // Pixel based JPG
        // $generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML(); // Pixel based HTML
        // $generatorHTML = new Picqer\Barcode\BarcodeGeneratorDynamicHTML();
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            // new TwigFilter('filter_name', [BarcodeRuntime::class, 'doSomething']),
            new TwigFilter('base64_encode',[BarcodeRuntime::class, 'base64_encode'])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('shipment_barcode',[BarcodeRuntime::class, 'generateShipmentBarcocde'])
        ];
    }
}
