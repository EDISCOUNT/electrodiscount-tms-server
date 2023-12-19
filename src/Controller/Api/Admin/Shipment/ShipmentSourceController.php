<?php

namespace App\Controller\Api\Admin\Shipment;

use App\Sourcing\ShipmentSourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentSourceController extends AbstractController
{
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager
    ){

    }

    #[Route('/api/admin/shipment/sources', name: 'app_api_admin_shipment_shipment_source')]
    public function index(): Response
    {
        $sources = $sources = $this->shipmentSourceManager->getSourcesArray();
        return $this->json([
            'sources' => $sources
        ]);
    }
}
