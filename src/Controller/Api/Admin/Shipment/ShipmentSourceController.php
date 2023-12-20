<?php

namespace App\Controller\Api\Admin\Shipment;

use App\Sourcing\ShipmentSourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentSourceController extends AbstractController
{
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager
    ) {
    }

    #[Route('/api/admin/shipment/sources', name: 'app_api_admin_shipment_shipment_source')]
    public function index(): Response
    {
        $sources = $sources = $this->shipmentSourceManager->getSourcesArray();
        return $this->json([
            'sources' => $sources
        ]);
    }


    #[Route('/api/admin/shipment/sources/query/by-code', name: 'app_api_admin_shipment_shipment_source_query_by_code',)]
    public function showByCode(Request $request): Response
    {
        $id = $request->query->get('code');
        $sources = $sources = $this->shipmentSourceManager->getSources();
        if (!isset($sources[$id])) {
            return $this->json([
                'error' => 'Source not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $source = $sources[$id];
        return $this->json($source);
    }

    
    // #[Route('/api/admin/shipment/sources/{id}', name: 'app_api_admin_shipment_shipment_source_show', requirements: ['id' => '[a-zA-Z0-9_-\.]+'], methods: ['GET'])]
    // public function show(string $id): Response
    // {
    //     $sources = $sources = $this->shipmentSourceManager->getSourcesArray();
    //     if (!isset($sources[$id])) {
    //         return $this->json([
    //             'error' => 'Source not found'
    //         ], Response::HTTP_NOT_FOUND);
    //     }
    //     $source = $sources[$id];
    //     return $this->json($source);
    // }
}
