<?php

namespace App\Controller\Api\Admin\Channel;

use App\Entity\Channel\Channel;
use App\Sourcing\ShipmentSourceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin/channel/channels/{channel}/orders', name: 'app_api_admin_channel_order')]
class OrderController extends AbstractController
{
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Channel $channel): Response
    {
        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);
        $orders = $repository->paginate();

        return $this->json($orders, context: [
            'groups' => [
                'order:list',
                'order:with_items',
                'order:with_channel',
                'order:with_address',
                'channel:list',
                'address:list',
                'order_item:read',
                'product:list'
            ],
        ]);
    }


    #[Route('/{id}/import-shipment', name: 'import_shipment', methods: ['POST'])]
    public function importShipment(Request $request, Channel $channel, string $id): Response
    {

        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);


        $order = $repository->getById($id);
        $shipment = $this->shipmentSourceManager->importShipmentForOrder($order);

        return $this->json($shipment, context: [
            'groups' => [
                'shipment:list',
                'shipment:with_items',
                'shipment:with_address',
                'address:list',
                'shipment_item:read',
                'shipment_item:with_product',
                'product:list'
            ],
        ]);
    }
}
