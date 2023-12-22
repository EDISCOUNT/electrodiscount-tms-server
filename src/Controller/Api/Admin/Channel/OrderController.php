<?php

namespace App\Controller\Api\Admin\Channel;

use App\Entity\Channel\Channel;
use App\Form\Order\BulkImportShipmentOrderType;
use App\Sourcing\ShipmentSourceManager;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Order\ImportShipmentOrderType;
use App\Entity\Carrier\Carrier;
use App\Entity\Shipment\Shipment;

#[Route('/api/admin/channel/channels/{channel}', name: 'app_api_admin_channel_order')]
class OrderController extends AbstractController
{
    public function __construct(
        private ShipmentSourceManager $shipmentSourceManager,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/orders', name: 'index', methods: ['GET'])]
    public function index(Request $request, Channel $channel): Response
    {


        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);
        $status = $request->query->get('limit', 'ALL');
        $filfilmentMethod = $request->query->get('fulfilment-method', 'ALL');

        if ($page < 1) {
            $page = 1;
        }
        if ($limit > 100) {
            $limit = 100;
        }


        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);
        $orders = $repository->paginate(
            page: $page,
            limit: $limit,
            criteria: [
                'status' => $status,
                'fulfilmentMethod' => $filfilmentMethod
            ]
        );

        $data = [...$orders->getCurrentPageResults()];
        $adapter = new ArrayAdapter($data);
        $pagination = new Pagerfanta($adapter);

        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);


        return $this->json($pagination, context: [
            'groups' => [
                'order:list',
                'order:with_items',
                'order:with_channel',
                'order:with_address',
                'channel:list',
                'address:list',
                'order_item:read',
                'order_item:with_fulfilment',
                'shipment_fulfilment:list',
                'shipment_fulfilment',
                'product:list'
            ],
        ]);
    }


    #[Route('/orders/{id}', name: 'show', methods: ['GET'])]
    public function show(Request $request, Channel $channel, string $id): Response
    {
        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);
        $order = $repository->getById($id);

        return $this->json($order, context: [
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


    #[Route('/import-order/{id}', name: 'import_order', methods: ['POST'])]
    public function importShipment(Request $request, Channel $channel, string $id): Response
    {

        $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);


        $order = $repository->getById($id);
        $shipment = $this->shipmentSourceManager->importShipmentForOrder($order, save: false);

        $form = $this->createForm(ImportShipmentOrderType::class, $shipment, ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);


        if ($form->isValid()) {
            $this->entityManager->persist($shipment);
            $this->entityManager->flush();

            return $this->json($shipment, Response::HTTP_CREATED, context: [
                'groups' => [
                    'shipment:list',
                    'shipment:with_items',
                    'shipment:with_address',
                    'shipment:with_carrier',
                    'carrier:list',
                    'address:list',
                    'shipment_item:read',
                    'shipment_item:with_product',
                    'product:list'
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }




    #[Route('/bulk-import-order', name: 'bulk_import_order', methods: ['POST'])]
    public function bulkImportShipment(Request $request, Channel $channel,): Response
    {
        $form = $this->createForm(BulkImportShipmentOrderType::class, options: ['csrf_protection' => false]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);


        if ($form->isValid()) {

            $repository = $this->shipmentSourceManager->getSourceManager($channel)->getOrderRepository($channel);

            /**
             * @var Shipment[]
             */
            $shipments = [];

            $data = $form->getData();
            /**
             * @var Carrier
             */
            $carrier = $data['carrier'];
            /**
             * @var string[]
             */
            $orderIds = $data['orders'];


            foreach ($orderIds as $orderId) {
                $order = $repository->getById($orderId);
                $shipment = $this->shipmentSourceManager->importShipmentForOrder($order, save: false);
                $shipment->setCarrier($carrier);
                $this->entityManager->persist($shipment);
            }

            $this->entityManager->flush();
            $shipmentIds = array_map(fn (Shipment $shipment) => $shipment->getId(), $shipments);

            return $this->json([
                'shipments' => $shipmentIds,
            ], Response::HTTP_CREATED, context: [
                'groups' => [
                    'shipment:list',
                    'shipment:with_items',
                    'shipment:with_address',
                    'shipment:with_carrier',
                    'carrier:list',
                    'address:list',
                    'shipment_item:read',
                    'shipment_item:with_product',
                    'product:list'
                ],
            ]);
        }

        return $this->json(['errors' => $this->getFormErrors($form)], Response::HTTP_BAD_REQUEST);
    }


    private function getFormErrors(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }
        return $errors;
    }
}